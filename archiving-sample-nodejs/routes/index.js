var http    = require('http'),
    OpenTok = require('opentok'),
    request = require('request'),
    url     = require('url'),
    uuid    = require('node-uuid');

exports.index = function(req, res, next) {
  res.render('index');
};

exports.hostView = function(req, res, next) {
  res.render('host-view', {
    tbjs: req.config["TB.js"],
    apiKey: req.config.apiKey,
    session: req.opentokSession,
    token: req.opentok.generateToken({
      session_id: req.opentokSession, 
      role:OpenTok.RoleConstants.MODERATOR,
      connection_data:"host"
    })
  });
};

exports.participantView = function(req, res, next) {
  res.render('participant-view', {
    tbjs: req.config["TB.js"],
    apiKey: req.config.apiKey,
    session: req.opentokSession,
    token: req.opentok.generateToken({
      session_id: req.opentokSession, 
      role:OpenTok.RoleConstants.PUBLISHER,
      connection_data:"participant"
    })
  });
};

var archivingID, lastArchivingStatus = { event: null, id: uuid.v4() };

var api = function(req, method, path, body, callback) {
  var rurl = req.config.apiEndpoint + "/v2/partner/" + req.config.apiKey + path;
  request({
    url: rurl,
    method: method,
    headers: {
      'X-TB-PARTNER-AUTH': req.config.apiKey + ":" + req.config.apiSecret
    },
    json: body
  }, callback);
};

exports.pastArchives = function(req, res, next) {
  var offset = parseInt(req.query.offset), perPage = 5;
  if(!offset || isNaN(offset)) {
    offset = 0;
  }

  api(req, 'GET', '/archive?offset=' + offset + '&count=5', null, function(err, response, body) {
    if(!err && body ) {
      try {
        body = JSON.parse(body);
      } catch (_err) {
        err = _err;
      }
    }
    if(err || response.statusCode != 200) {
      console.log("Response", response.statusCode);
      next(Error("Unexpected response from OpenTok"));
      console.log("body", body);
    } else {
      var showPrevious, showNext;
      if(offset > 0) {
        showPrevious = "/past-archives?offset=" + ((offset - perPage > 0) ? offset - perPage : 0);
      }
      if(body.count > offset + perPage) {
        showNext = "/past-archives?offset=" + (offset + perPage);
      }
      res.render('past-archives', {
        offset: offset,
        archives: body,
        showPrevious: showPrevious,
        showNext: showNext,
        lastArchivingStatus: lastArchivingStatus
      });
    }
  });
};

exports.downloadArchive = function(req, res, next) {
  api(req, 'GET', '/archive/' + encodeURIComponent(req.query.id), null, function(err, response, body) {
    if(!err && body ) {
      try {
        body = JSON.parse(body);
      } catch (_err) {
        err = _err;
      }
    }
    if(err || response.statusCode != 200) {
      if(response && response.statusCode == 404) {
        next(Error("Archive not found"));
      } else {
        next(Error("Unexpected response from OpenTok"));        
      }
      console.log("Error:", err && err.message || response && response.statusCode || body);      
    } else {    
            
      if(!body.url) {
        res.send("NO URL");
        return;
      }
      
      var movieURL = url.parse(body.url);
      

      var movieReq = http.get({
        hostname: movieURL.hostname,
        port: movieURL.port || 80,
        path: movieURL.pathname
      }, function(movieRes) {
        if(movieRes.statusCode == 404) {
          next(Error("Archive file not found"));
          return;
        }
        if(movieRes.statusCode == 200) {
          res.setHeader('Content-Disposition', 'attachment; filename=archive-' + encodeURIComponent(req.query.id) + ".mp4");          
        }
        res.setHeader("Server", "Archiving Sample App");
        var skipKeys = ['x-amz-id-2', 'x-amz-request-id', 'server', 'content-disposition'];
        for(var key in movieRes.headers) {
          if(movieRes.headers.hasOwnProperty(key) && skipKeys.indexOf(key) < 0) {
            res.setHeader(key, movieRes.headers[key]);
          }
        }
        res.writeHead(movieRes.statusCode);
        movieRes.pipe(res);
      }).on('error', function(err) {
        util.log("Unable to get archive " + body.url + ": " + err.message);
        next(Error("Error getting archive"));
      });
      
    }
  });
}

exports.deleteArchive = function(req, res, next) {
  api(req, 'DELETE', '/archive/' + encodeURIComponent(req.query.id), null, function(err, response, body) {
    if(err || response.statusCode != 204) {
      next(Error("Unexpected response from OpenTok"));
      console.log("body", response.statusCode, body, err && err.message);
    } else {
      res.redirect("/past-archives");
    }
  });
}

exports.startArchive = function(req, res, next) {
  api(req, "POST", "/archive", {
    action: "start", 
    sessionId: req.opentokSession,
    name: uuid.v4()
  }, function(err, response, body) {
    if(err) {
      next(err);
    } else if(!body || body.status != 'started') {
      next(Error("Unexpected response from OpenTok"));
      console.error("Response", response.statusCode, "Body", body);
    } else {
      res.send({
        status: "started",
        id: body.id
      });
      archivingID = body.id;
    }
  });
};

exports.stopArchive = function(req, res, next) {
  api(req, 'POST', "/archive/" + archivingID, {
    action: "stop"
  }, function(err, response, body) {
    if(err) {
      next(err);
    } else if(!body || body.status != 'stopped') {
      next(Error("Unexpected response from OpenTok"));
      console.error("Response", response.url, response.statusCode, "Body", body);
    } else {
      res.send({
        status: "stopped",
        id: body.id
      });
      archivingID = null;
    }
  });
};

exports.archivingEvent = function(req, res, next) {
  // This should really only have to look at sessionId
  // But that appears not to be populated?
  if(req.body && req.body.id && req.body.sessionId === req.opentokSession || !archivingID || req.body.id == archivingID) {
    // This is an archiving event I care about.
    if(req.body.status == 'started') {
      archivingID = req.body.id;
    } else if(req.body.status == 'stopped') {
      archivingID = null;
    }
  }
  lastArchivingStatus = { event: req.body, id: uuid.v4() };
  res.send({ok:true});
};

exports.lastArchivingStatus = function(req, res, next) {
  res.send(lastArchivingStatus);
};
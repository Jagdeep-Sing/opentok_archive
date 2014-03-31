var ejsLocals = require('ejs-locals'),
    express   = require('express'),
    fs        = require('fs'),
    NeDB      = require('nedb'),
    OpenTok   = require('opentok'),
    path      = require('path'),
    util      = require('util'),

    routes    = require('./routes');

var config = {};

if (process.env.PORT) {
  config = {
    port: process.env.PORT,
    apiKey: process.env.API_KEY,
    apiSecret: process.env.API_SECRET,
    callbackHost: process.env.CALLBACK_HOST,
    "TB.js": process.env.TB_JS,
    apiEndpoint: process.env.API_ENDPOINT,
    anvil: process.env.ANVIL
  }
} else {
  try {
    config = JSON.parse(fs.readFileSync("./config/server.json"));
  } catch(err) {
    console.error("Error reading config/server.json - have you copied config/server.json.sample to config/server.json?", err.message);
    process.exit();
  }  
}

if(!config["TB.js"]) {
  config["TB.js"] = "https://swww.tokbox.com/webrtc/v2.2/js/TB.min.js";
}

if(!config.apiEndpoint) {
  config.apiEndpoint = "https://api.opentok.com";
}


if(!(config.apiKey && config.apiSecret)) {
  console.error("You must set   apiKey and apiSecret in config.json");
  process.exit();
}

util.log("Starting Archiving Sample App " + JSON.parse(fs.readFileSync("./package.json")).version);

var app = express();

app.engine('html', ejsLocals);
app.set('view engine', 'html');
app.set('views', __dirname + '/views');

app.request.db = new NeDB({
    filename: path.resolve(__dirname, "data/app.data"),
    autoload: true
});

app.request.config = config;

app.use(express.static(__dirname + "/public"));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);

// Home page
app.get("/", routes.index);

app.get("/host-view", routes.hostView);
app.get("/start-archive", routes.startArchive);
app.get("/stop-archive", routes.stopArchive);

app.get("/participant-view", routes.participantView);

app.get("/past-archives", routes.pastArchives);
app.get("/download-archive", routes.downloadArchive);
app.get("/delete-archive", routes.deleteArchive);

app.get("/archiving-event", routes.lastArchivingStatus);
app.post("/archiving-event", routes.archivingEvent);

// Error handling
// assume "not found" in the error msgs is a 404. 
app.use(function(err, req, res, next){
  // treat as 404
  if (~err.message.indexOf('not found')) return next();

  // log it
  console.error(err.stack);

  // error page
  res.status(500).render('5xx');
});

app.use(function(req, res, next){
  res.status(404).render('404', { url: req.originalUrl });
});

var opentok = new OpenTok.OpenTokSDK(config.apiKey, config.apiSecret);
if(config.anvil) {
  opentok.api_url = config.anvil;
}
app.request.opentok = opentok;

opentok.createSession('', function(result){
  app.request.opentokSession = result;
  app.listen(config.port, function(){
    util.log("Up and running at http://localhost:" + config.port + "/");
  });
});

<?php

include 'lib/get_token.php';
include 'layout.php';

sample_header();

?>

  <script src="https://swww.tokbox.com/webrtc/v2.2/js/TB.min.js"></script>

  <div class="container bump-me">

    <div class="body-content">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Host</h3>
        </div>
        <div class="panel-body">
          <div id="subscribers"><div id="publisher"></div></div>
        </div>
        <div class="panel-footer">
          <button class="btn btn-danger start">Start archiving</button>
          <button class="btn btn-success stop">Stop archiving</button>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Instructions</h3>
      </div>
      <div class="panel-body">
        <p>
          Click <strong>Start archiving</strong> to begin archiving this session.
          All publishers in the session will be included, and all publishers that
          join the session will be included as well.
        </p>
        <p>
          Click <strong>Stop archiving</strong> to end archiving this session.
          You can then go to <a href="/past-archives">past archives</a> to
          view your archive (once its status changes to available).
        </p>
          <table class="table">
            <thead>
              <tr>
                <th>When</th>
                <th>You will see</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="vertical-align: middle;">Archiving is started</td>
                <td><img src="img/archiving-on-message.png"></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;">Archiving remains on</td>
                <td><img src="img/archiving-on-idle.png"></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;">Archiving is stopped</td>
                <td><img src="img/archiving-off.png"></td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
  </div>
  
    <script>
      var session = TB.initSession(<?php echo(json_encode($config_session_id)); ?>),
          publisher = TB.initPublisher(<?php echo(json_encode($config_api_key)); ?>, document.querySelector("#publisher")),
          subscribers = document.querySelector("#subscribers"),
          archiveID = null;

      var subscribe = function(stream) {
        var el = document.createElement("div");
        subscribers.appendChild(el)
        session.subscribe(stream, el);
      }

      session.connect(<?php echo(json_encode($config_api_key)); ?>, <?php echo(json_encode($token)); ?>, function(err, info) {
        if(err) {
          alert(err.message || err);
        }

        info.streams.forEach(function(stream) {
          if(stream.connection.connectionId != session.connection.connectionId) {
            subscribe(stream);
          }
        });

        session.publish(publisher);
      });

      session.on('streamCreated', function(event) {
        event.streams.forEach(function(stream) {
          if(stream.connection.connectionId != session.connection.connectionId) {
            subscribe(stream);
          }
        });
      });
      
      session.on('archiveStarted', function(event) {
        archiveID = event.id;
        console.log("ARCHIVE STARTED");
        $(".start").hide();
        $(".stop").show();
      });
      
      session.on('archiveStopped', function(event) {
        archiveID = null;
        console.log("ARCHIVE STOPPED");
        $(".start").show();
        $(".stop").hide();
      });
      
      session.on('archiveUpdated', function(event) {
        console.log("ARCHIVE UPDATED");
      });
      
      session.on('archiveDestroyed', function(event) {
        console.log("ARCHIVE DESTROYED");
      });

      $(document).ready(function() {
        $(".start").click(function(event){
          $.get("start-archive.php");
        });
        $(".stop").click(function(event){
          $.get("stop-archive.php?id=" + archiveID);
        });
        $(".start").show();
        $(".stop").hide();
      });

    </script>

  </div>

<?php

sample_footer();

?>

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
          <h3 class="panel-title">Participant</h3>
        </div>
        <div class="panel-body">
          <div id="subscribers"><div id="publisher"></div></div>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Instructions</h3>
      </div>
      <div class="panel-body">
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
          subscribers = document.querySelector("#subscribers");

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
        console.log("ARCHIVE STARTED");
      });
      
      session.on('archiveStopped', function(event) {
        console.log("ARCHIVE STOPPED");
      });
      
      session.on('archiveUpdated', function(event) {
        console.log("ARCHIVE UPDATED");
      });
      
      session.on('archiveDestroyed', function(event) {
        console.log("ARCHIVE DESTROYED");
      });

    </script>

  </div>

<?php

sample_footer();

?>

<?php

include 'config.php';
include 'lib/webrtc_archiving_beta.php';

$apiObj = new OpenTokArchivingInterface($config_api_key, $config_api_secret);

$res = $apiObj->stopArchivingSession($_REQUEST['id']);
echo(json_encode($res));

?>

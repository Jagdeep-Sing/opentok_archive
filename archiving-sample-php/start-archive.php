<?php

include 'config.php';
include 'lib/webrtc_archiving_beta.php';

$apiObj = new OpenTokArchivingInterface($config_api_key, $config_api_secret);

$res = $apiObj->startArchivingSession($config_session_id, "PHP Archiving Sample App");
echo(json_encode($res));

?>

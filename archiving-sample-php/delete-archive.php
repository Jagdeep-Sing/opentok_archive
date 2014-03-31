<?php

include 'config.php';
include 'lib/webrtc_archiving_beta.php';

$apiObj = new OpenTokArchivingInterface($config_api_key, $config_api_secret);

$res = $apiObj->deleteArchive($_REQUEST['id']);

if($res->status == 204) {
  header('Location: past-archives.php');
} else {
  echo(json_encode($res));
}

?>

archiving-sample-php
====================

A sample showing how to use the OpenTok 2.0 archiving API, written in PHP.

## Prerequisites

* PHP 5.2 or higher. (Tested against 5.4.17).
* PHP must not have been compiled with `--disable-json=true`.
* PHP must be configured to allow `file_get_contents` to fetch remote URLs.
* An OpenTok API key, secret and session (from [dashboard.tokbox.com][dashboard]).

## To run this app for yourself

1. Clone this repo.
2. Copy config.php.sample to config.php (in the same folder) and set the following:
    * `config_api_key` -- Your OpenTok API key
    * `config_api_secret` -- Your OpenTok secret
    * `config_session_id` -- An OpenTok Session ID
    * `date_default_timezone_set` -- Set timezone to use for dates. See the PHP website for the
      [list of supported timezones][timezones].
3. Copy this repo to the location where your web server can serve it. (The entire folder should be reachable, not just
   the public folder.)

## Then time to test!

1. Open http://yourserver/path-to-this-folder/index.php in Chrome or Firefox 22+.
2. Click the "Host view" button. The Host View page publishes an audio-video stream to an OpenTok session.
   It also includes controls that cause the web server to start and stop archiving the session, by calling
   the OpenTok 2.0 archiving REST API.
3. Click the Allow button to grant access to the camera and microphone.
4. Click the "Start archiving" button. The session starts recording to an archive. Note that the red archiving
   indicator is displayed in the video view.
5. Open http://localhost:3052/ in a new browser tab. (You may want to mute your computer speaker to prevent
   feedback. You are about to publish two audio-video streams from the same computer.)
6. Click the "Participant view" button. The page connects to the OpenTok session, displays the existing
   stream (from the Host View page)
7. Click the Allow button in the page to grant access to the camera and microphone. The page publishes a new stream
   to the session. Both streams are now being recorded to an archive.
8. On the Host View page, click the "Stop archiving" button.
9. Click the "past archives" link in the page. This page lists the archives that have been recorded. Note that
   it may take up to 2 minutes for the video file to become available (for a 90-minute recording).
10. Click a listing for an available archive to download the MP4 file of the recording.

## Known issues

* Recordings of streams from mobile devices do not adjust video orientation when the device orientation changes.
* Archive recordings are MP4 files with H.264 video and AAC audio. Some browsers, such as Firefox on Mac OS, do
not support playback of this format in an HTML video tag. As a workaround, you can use Adobe Flash Player to load
the video.
* The URLs for recorded archives are not secure, and they may change during or after the beta period.

## Documentation

* [Archiving REST API documentation](docs/REST-API.md)
* [Archiving JavaScript API documentation](docs/JavaScript-API.md)

## More information

The OpenTok 2.0 archiving feature is currently in beta testing.

If you have questions or to provide feedback, please write <denis@tokbox.com>.

[dashboard]: https://dashboard.tokbox.com/
[timezones]: http://us1.php.net/manual/en/timezones.php

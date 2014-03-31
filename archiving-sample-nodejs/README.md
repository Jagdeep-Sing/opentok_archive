OpenTok 2.0 Archiving Sample App -- Node.js
===========================================

A sample app showing use of the OpenTok 2.0 archiving API.

This sample app is for use with a Node.js web server. 

## Prerequisites

1. node.js v0.10.x (we recommend v0.10.21)
   The OpenTok archiving API does not require node.js. However, this sample uses node.js as a web server
   and to make calls to the OpenTok REST API.
2. An OpenTok API key and secret (see <https://dashboard.tokbox.com>)

## Setup

1. Copy config/server.json.sample file to config/server.json and set the following values:
   * apiKey -- Set this to your OpenTok API key (see <https://dashboard.tokbox.com>).
   * apiSecret -- Set this to your OpenTok API secret (see <https://dashboard.tokbox.com>).
2. From the project root, run `npm install` to install the app and its dependencies.

## To run the app for yourself

From the project root, run `node app` to start the app.

Then test the app:

1. Open http://localhost:3052/ (or your server URL) in a web browser.
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
* [How to deploy the sample app on Heroku](docs/Heroku.md)

## More information

The OpenTok 2.0 archiving feature is currently in beta testing.

If you have questions or to provide feedback, please write <denis@tokbox.com>.

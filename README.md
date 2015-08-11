Video Converter
===========================

REST-service for converting flv to mp4

Routing
-------------------

POST /videos/upload - upload file

file - post-parameter

GET /videos - list of user video

GET /videos/{id} - info of video

GET /videos/{id}/donwload - download video

DELETE /videos/{id} - delete video


Console
-------------------
yii worker/convert - find video when need convert (for cron)

yii worker/convert 1 - convert video with id 1
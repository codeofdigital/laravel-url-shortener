<?php

use GuzzleHttp\Psr7\Message;

return Message::parseResponse(trim('
HTTP/1.1 200 OK
Server: nginx
Date: Mon, 14 Jun 2021 16:55:09 GMT
Content-Type: application/json
Content-Length: 263
Connection: keep-alive
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-XSS-Protection: 1; mode=blockFilter
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Content-Security-Policy: default-src \'none

{"created_at":"2021-06-14T13:46:33+0000","id":"bit.ly/3iSAOvF","link":"https://bit.ly/3iSAOvF","custom_bitlinks":[],"long_url":"https://laravel.com/","archived":false,"tags":[],"deeplinks":[],"references":{"group":"https://api-ssl.bitly.com/v4/groups/Bl6a5s21gp1"}}
'));

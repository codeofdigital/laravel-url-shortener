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

{"url":{"status":7,"fullLink":"https://laravel.com","date":"2021-06-15","shortLink":"https://cutt.ly/lnHBR1m","title":"Laravel - The PHP Framework For Web Artisans"}}
'));

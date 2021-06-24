<?php

use GuzzleHttp\Psr7\Message;

return Message::parseResponse(trim('
HTTP/1.1 200 OK
Server: nginx
Date: Mon, 24 Jun 2021 12:43:12 GMT
Content-Type: application/json
Content-Length: 263
Connection: keep-alive
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-XSS-Protection: 1; mode=blockFilter
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Content-Security-Policy: default-src \'none

{"result_url":"https:\/\/hideuri.com\/Oky9AE"}
'));

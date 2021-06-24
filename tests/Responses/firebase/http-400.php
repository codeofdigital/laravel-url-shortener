<?php

use GuzzleHttp\Psr7\Message;

return Message::parseResponse(trim('
HTTP/1.1 400 Bad Request
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

{
  "error": {
    "code": 400,
    "message": "API key not valid. Please pass a valid API key.",
    "status": "INVALID_ARGUMENT",
    "details": [
      {
        "type": "type.googleapis.com/google.rpc.ErrorInfo",
        "reason": "API_KEY_INVALID",
        "domain": "googleapis.com",
        "metadata": {
          "service": "firebasedynamiclinks.googleapis.com"
        }
      }
    ]
  }
}
'));

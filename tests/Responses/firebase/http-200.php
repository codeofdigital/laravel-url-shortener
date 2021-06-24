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

{
  "shortLink": "https://codeofdigital.page.link/CBNYHKkLc6FZmuYU9",
  "warning": [
    {
      "warningCode": "UNRECOGNIZED_PARAM",
      "warningMessage": "There is no configuration to prevent phishing on this domain https://codeofdigital.page.link. Setup URL patterns to whitelist in the Firebase Dynamic Links console. [https://support.google.com/firebase/answer/9021429]"
    }
  ],
  "previewLink": "https://codeofdigital.page.link/CBNYHKkLc6FZmuYU9?d=1"
}
'));


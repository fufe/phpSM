<?php

require 'config.php';

$salt = $CONFIG['salt'];
$app = isset($_REQUEST['app']) ? $_REQUEST['app'] : '';
$stream = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';

$md5 = base64_encode(md5($salt . $app . '/' . $stream, true)); // Using binary hashing.
$md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
$md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.


if ($key == $md5) {
//    header("HTTP/1.1 200 OK");
    http_response_code(200);
} else {
//    header("HTTP/1.1 500 Internal Server Error");
    http_response_code(503);
}

<?php

require 'config.php';

$salt = $CONFIG['salt'];
$app = isset($_REQUEST['app']) ? $_REQUEST['app'] : '';
$stream = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$st = isset($_REQUEST['st']) ? $_REQUEST['st'] : '';
$expire = isset($_REQUEST['e']) ? (int)$_REQUEST['e'] : 0;

$md5 = base64_encode(md5($salt . $app . '/' . $stream . $expire, true)); // Using binary hashing.
$md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
$md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.


if ($st == $md5 && $expire >= time()) {
//    header("HTTP/1.1 200 OK");
    http_response_code(200);
} else {
//    header("HTTP/1.1 500 Internal Server Error");
    http_response_code(503);
}

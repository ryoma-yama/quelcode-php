<?php
$limit = $_GET['target'];

// getParameterが1以上の整数でないなら400:BadRequestを返す
if (!ctype_digit($limit) || $limit === '0') {
    http_response_code(400);
    exit();
}

$dsn = 'mysql:dbname=test;host=mysql';
$dbuser = 'test';
$dbpassword = 'test';

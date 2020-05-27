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

// DBと接続する
try {
    $db = new PDO($dsn . ';charset=utf8', $dbuser, $dbpassword);
} catch (PDOException $exception) {
    echo 'DB接続エラー: ' . $exception->getMessage();
}

// DBから値を取得する
$records = $db->query('SELECT value FROM prechallenge3');
$record = $records->fetchAll(PDO::FETCH_COLUMN, "value");

// 比較のためにで要素の型を変換する
foreach ($record as $records) {
    $recordInt[] += $records;
}
$limitInt += $limit;

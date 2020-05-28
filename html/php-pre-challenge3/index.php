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
    http_response_code(500);
    exit();
}

// DBから値を取得する
$records = $db->query('SELECT value FROM prechallenge3');
$record = $records->fetchAll(PDO::FETCH_COLUMN, "value");

// 比較のために要素の型を変換する
foreach ($record as $records) {
    $array[] += $records;
}
$limitInt += $limit;

// 組み合わせを取得する関数の定義
function combinations($array, $chosen)
{
    $length = count($array);
    if ($length < $chosen) {
        // 要素よりも選ぶ数が多い場合
        return;
    } elseif ($chosen === 1) {
        // 一つを選ぶ場合 
        for ($i = 0; $i < $length; $i++) {
            $result[$i] = [$array[$i]];
        }
    } else {
        // 一つより多く選ぶ場合
        for ($i = 0; $i < $length - $chosen + 1; $i++) {
            $parts = combinations(array_slice($array, $i + 1), $chosen - 1);
            foreach ($parts as $part) {
                array_unshift($part, $array[$i]);
                $result[] = $part;
            }
        }
    }
    return $result;
}

// 結果をjsonで出力する
echo json_encode($arrTest2);

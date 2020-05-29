<?php
$limit = $_GET['target'];

// getParameterが1以上の整数でないなら400:BadRequestを返す
if (!ctype_digit($limit) || substr($limit, 0, 1) === '0') {
    http_response_code(400);
    exit();
}

$dsn = 'mysql:dbname=test;host=mysql;charset=utf8';
$dbuser = 'test';
$dbpassword = 'test';

// DBと接続する
try {
    $db = new PDO($dsn, $dbuser, $dbpassword);
} catch (PDOException $exception) {
    http_response_code(500);
    exit();
}

// DBから値を取得する
$records = $db->query('SELECT value FROM prechallenge3');
$record = $records->fetchAll(PDO::FETCH_COLUMN, "value");

// 比較のために要素の型を変換する
$array = [];
foreach ($record as $records) {
    $array[] += $records;
}

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

// 全組み合わせを取得する
$length = count($array);
for ($i = 1; $i < $length + 1; $i++) {
    $chosen = $i;
    $resultSet[] = combinations($array, $chosen);
}
// 比較のために全組み合わせを一つの配列に統合する
for ($i = 0; $i < $length - 1; $i++) {
    $resultSet[0] = array_merge($resultSet[0], $resultSet[$i + 1]);
}

// getParameterと比較する
$limitInt = 0;
$limitInt += $limit;
$output = [];
foreach ($resultSet[0] as $subArray) {
    $subArraySum = array_sum($subArray);
    if ($limitInt === $subArraySum) {
        $output[] = $subArray;
    }
}

// 結果をjsonで出力する
echo json_encode($output);

// 参考先は下記です
// https://stabucky.com/wp/archives/2188

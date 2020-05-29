<?php
$targetValue = $_GET['target'];

// getParameterが1以上の整数でないなら400:BadRequestを返す
if (!ctype_digit($targetValue) || substr($targetValue, 0, 1) === '0') {
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
$statement = $db->query('SELECT value FROM prechallenge3');
$records = $statement->fetchAll(PDO::FETCH_COLUMN, "value");

// 比較のために要素の型を変換する
$origins = [];
foreach ($records as $record) {
    $origins[] += $record;
}

// 組み合わせを取得する関数の定義
function generateCombinations($origins, $choose)
{
    $length = count($origins);
    if ($length < $choose) {
        // 要素よりも選ぶ数が多い場合
        return;
    } elseif ($choose === 1) {
        // 一つを選ぶ場合 
        for ($i = 0; $i < $length; $i++) {
            $result[$i] = [$origins[$i]];
        }
    } else {
        // 一つより多く選ぶ場合
        for ($i = 0; $i < $length - $choose + 1; $i++) {
            $parts = generateCombinations(array_slice($origins, $i + 1), $choose - 1);
            foreach ($parts as $part) {
                array_unshift($part, $origins[$i]);
                $result[] = $part;
            }
        }
    }
    return $result;
}

// 全組み合わせを取得する
$length = count($origins);
for ($i = 1; $i < $length + 1; $i++) {
    $choose = $i;
    $combinations[] = generateCombinations($origins, $choose);
}
// 比較のために全組み合わせを一つの配列に統合する
for ($i = 0; $i < $length - 1; $i++) {
    $combinations[0] = array_merge($combinations[0], $combinations[$i + 1]);
}

// getParameterと比較する
$output = [];
foreach ($combinations[0] as $combination) {
    $subArraySum = array_sum($combination);
    if ($targetValue == $subArraySum) {
        $output[] = $combination;
    }
}

// 結果をjsonで出力する
echo json_encode($output);

// 参考先は下記です
// https://stabucky.com/wp/archives/2188

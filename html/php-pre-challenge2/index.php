<?php
$array = explode(',', $_GET['array']);

// 修正はここから
$elements = count($array);
$times = $elements - 1;
for ($i = 0; $i < $elements; $i++) {
    for ($j = 0; $j < $times - $determined; $j++) {
        if ($array[$j] > $array[$j + 1]) {
            $replacement = $array[$j];
            $array[$j] = $array[$j + 1];
            $array[$j + 1] = $replacement;
        }
    }
    $determined++;
}
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";

<?php
$array = explode(',', $_GET['array']);

// 修正はここから
$elements = count($array);
for ($i = 0; $i < $elements; $i++) {
    for ($j = 1; $j < $elements; $j++) {
        if ($array[$i] > $array[$i + $j] && $array[$i + $j] !== null) {
            $replacement = $array[$i];
            $array[$i] = $array[$i + $j];
            $array[$i + $j] = $replacement;
        }
    }
}
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";

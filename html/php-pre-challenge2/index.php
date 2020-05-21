<?php
$array = explode(',', $_GET['array']);

// 修正はここから
$elements = count($array);
for ($i = 0; $i < $elements; $i++) {
    for ($j = 0; $j < $elements - 1; $j++) {
        if ($array[$j] > $array[$j + 1] && $array[$j + 1] !== null) {
            $replacement = $array[$j];
            $array[$j] = $array[$j + 1];
            $array[$j + 1] = $replacement;
        }
    }
}
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";

<?php
$array = explode(',', $_GET['array']);

// 修正はここから
for ($i = 0; $i < count($array); $i++) {
  if ($array[$i] > $array[$i + 1] && $array[$i + 1] !== null) {
    $replacement = $array[$i];
    $array[$i] = $array[$i + 1];
  }
}
// 修正はここまで

echo "<pre>";
print_r($array);
echo "</pre>";

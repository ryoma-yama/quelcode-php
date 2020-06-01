<?php
require 'dbconnect.php';
$id = $_REQUEST['id'];

$like = $db->prepare('UPDATE posts SET likeFlag=true WHERE id=?');
$like->execute([$id]);

header('Location: index.php');
exit();

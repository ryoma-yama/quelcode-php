<?php
session_start();
require 'dbconnect.php';

// このページに直接飛んできてないかどうか
if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    $like = $db->prepare('INSERT INTO likes SET member_id=?, post_id=?');
    $like->execute([$_SESSION['id'], $id]);
}

header('Location: index.php');
exit();

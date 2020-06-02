<?php
session_start();
require 'dbconnect.php';
$id = $_REQUEST['id'];

// 複製する投稿のDataを取得する
$getMessage = $db->prepare('SELECT * FROM posts WHERE id=?');
$getMessage->execute([$id]);
$message = $getMessage->fetch();

header('Location: index.php');
exit();

<?php
session_start();
require 'dbconnect.php';
$id = $_REQUEST['id'];

// 複製する投稿のDataを取得する
$getMessage = $db->prepare('SELECT message, member_id, reply_post_id, created FROM posts WHERE id=?');
$getMessage->execute([$id]);
$message = $getMessage->fetch();

// 投稿を複製する
$retweet = $db->prepare('INSERT INTO posts SET message=?, member_id=?, reply_post_id=?, created=?');
$retweet->execute([
    $message['message'],
    $message['member_id'],
    $message['reply_post_id'],
    $message['created']
]);

header('Location: index.php');
exit();

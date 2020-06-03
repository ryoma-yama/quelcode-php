<?php
session_start();
require 'dbconnect.php';

// このページに直接飛んできてないかどうか
if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    // 複製する投稿のDataを取得する
    $getMessage = $db->prepare('SELECT message, member_id, reply_post_id, retweet_member_id, created FROM posts WHERE id=?');
    $getMessage->execute([$id]);
    $message = $getMessage->fetch();

    // postsTableにあって
    if ($message['member_id'] === $_SESSION['id'] && $message['retweet_member_id'] !== '0') {
        // urlParameterでdisが渡されていれば
        if ($_REQUEST['option'] === 'dis') {
            // リツイートを取り消す
            $disRetweet = $db->prepare('DELETE FROM posts WHERE id=?');
            $disRetweet->execute([$id]);
        }
    } else {
        // postsTableになければ, 投稿を複製する
        $retweet = $db->prepare('INSERT INTO posts SET message=?, member_id=?, reply_post_id=?, retweet_member_id=?, retweet_post_id=?, created=?');
        $retweet->execute([
            $message['message'],
            $_SESSION['id'],
            $message['reply_post_id'],
            $message['member_id'],
            $id,
            $message['created']
        ]);
    }
}

header('Location: index.php');
exit();

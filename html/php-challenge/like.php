<?php
session_start();
require 'dbconnect.php';

// このページに直接飛んできてないかどうか
if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    // リツイート元の投稿を参照するために, retweet_post_idを取得する
    $messagesForLike = $db->prepare('SELECT retweet_post_id FROM posts WHERE id=?');
    $messagesForLike->execute([$id]);
    $messageForLike = $messagesForLike->fetch();

    // リツイートされていない投稿の, リツイート元の投稿のidには, その投稿のidが入るようにする
    if ($messageForLike['retweet_post_id'] === '0') {
        $messageForLike['retweet_post_id'] = $id;
    }

    // 投稿を検査するための準備
    $messages = $db->prepare('SELECT * FROM likes WHERE member_id=? AND post_id=?');
    $messages->execute([$_SESSION['id'], $messageForLike['retweet_post_id']]);
    $message = $messages->fetch();

    // いいねtableにあって
    if ($message['member_id'] === $_SESSION['id']) {
        // urlParameterでdisが渡されていれば
        if ($_REQUEST['option'] === 'dis') {
            // よくないねをする
            $dislike = $db->prepare('DELETE FROM likes WHERE member_id=? AND post_id=?');
            $dislike->execute([$_SESSION['id'], $messageForLike['retweet_post_id']]);
        }
    } else {
        // いいねtableになければ, いいねをする
        $like = $db->prepare('INSERT INTO likes SET member_id=?, post_id=?');
        $like->execute([$_SESSION['id'], $messageForLike['retweet_post_id']]);
    }
}

header('Location: index.php');
exit();

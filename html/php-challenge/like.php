<?php
session_start();
require 'dbconnect.php';

// このページに直接飛んできてないかどうか
if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    // 投稿を検査するための準備
    $messages = $db->prepare('SELECT * FROM likes WHERE member_id=? AND post_id=?');
    $messages->execute([$_SESSION['id'], $id]);
    $message = $messages->fetch();

    // いいねtableにあって
    if ($message['member_id'] === $_SESSION['id']) {
        // urlParameterでdisが渡されていれば
        if ($_REQUEST['option'] === 'dis') {
            // よくないねをする
            $dislike = $db->prepare('DELETE FROM likes WHERE member_id=? AND post_id=?');
            $dislike->execute([$_SESSION['id'], $id]);
        }
    } else {
        // いいねtableになければ, いいねをする
        $like = $db->prepare('INSERT INTO likes SET member_id=?, post_id=?');
        $like->execute([$_SESSION['id'], $id]);
    }
}

header('Location: index.php');
exit();

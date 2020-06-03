<?php
session_start();
require 'dbconnect.php';

// このページに直接飛んできてないかどうか
if (isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    // リツイートを取り消す場合
    if ($_REQUEST['option'] === 'dis') {
        // 投稿を検査するための準備
        $getMessage = $db->prepare('SELECT retweet_member_id FROM posts WHERE id=?');
        $getMessage->execute([$id]);
        $message = $getMessage->fetch();

        // 投稿が自分のリツイートしたものであれば
        if ($message['retweet_member_id'] === $_SESSION['id']) {
            // リツイートを取り消す
            $disRetweet = $db->prepare('DELETE FROM posts WHERE id=?');
            $disRetweet->execute([$id]);
        }

        // 自分のリツイートした投稿からリツイートを取り消す
        $getMessage = $db->prepare('SELECT id FROM posts WHERE retweet_member_id=? AND retweet_post_id=?');
        $getMessage->execute([$_SESSION['id'], $id]);
        $target = $getMessage->fetch();
        $disRetweetFromOrigin = $db->prepare('DELETE FROM posts WHERE id=?');
        $disRetweetFromOrigin->execute([$target['id']]);
    }

    // リツイートする場合
    if ($_REQUEST['option'] === 'on') {
        // 既にRTしてないかどうかを検査する準備
        $getMessage = $db->prepare('SELECT id FROM posts WHERE retweet_member_id=? AND retweet_post_id=?');
        $getMessage->execute([$_SESSION['id'], $id]);
        $target = $getMessage->fetch();

        // 重複がなければ
        if (!$target) {
            // 複製する投稿のDataを取得する
            $getMessage = $db->prepare('SELECT message, member_id, reply_post_id, retweet_member_id, created FROM posts WHERE id=?');
            $getMessage->execute([$id]);
            $message = $getMessage->fetch();

            // 投稿を複製する
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
}

header('Location: index.php');
exit();

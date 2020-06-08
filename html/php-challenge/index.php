<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
	// ログインしている
	$_SESSION['time'] = time();

	$members = $db->prepare('SELECT * FROM members WHERE id=?');
	$members->execute(array($_SESSION['id']));
	$member = $members->fetch();
} else {
	// ログインしていない
	header('Location: login.php');
	exit();
}

// 投稿を記録する
if (!empty($_POST)) {
	if ($_POST['message'] != '') {
		$message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
		$message->execute(array(
			$member['id'],
			$_POST['message'],
			$_POST['reply_post_id']
		));

		header('Location: index.php');
		exit();
	}
}

// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
	$page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.modified DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
	$response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
	$response->execute(array($_REQUEST['res']));

	$table = $response->fetch();
	$message = '@' . $table['name'] . ' ' . $table['message'];
}

// htmlspecialcharsのショートカット
function h($value)
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定します
function makeLink($value)
{
	return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
	<script src="https://kit.fontawesome.com/3cc129bd3b.js" crossorigin="anonymous"></script>
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>ひとこと掲示板</h1>
		</div>
		<div id="content">
			<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
			<form action="" method="post">
				<dl>
					<dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
					<dd>
						<textarea name="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
						<input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
					</dd>
				</dl>
				<div>
					<p>
						<input type="submit" value="投稿する" />
					</p>
				</div>
			</form>

			<?php
			foreach ($posts as $post) :
			?>
				<div class="msg">
					<!-- リツイートをした人の表示 -->
					<?php
					// リツイートをした人が誰かを取得する
					$whoRetweets = $db->prepare('SELECT m.name FROM members m, posts p WHERE m.id=p.retweet_member_id AND p.id=?');
					$whoRetweets->execute([$post['id']]);
					$whoRetweet = $whoRetweets->fetch();
					?>
					<?php if ($post['retweet_member_id'] === $_SESSION['id']) : ?>
						<p class="retweetedSign"><i class="fas fa-retweet my-retweet"></i>リツイート済</p>
					<?php elseif ($post['retweet_member_id'] > 0) : ?>
						<p class="retweetedSign"><i class="fas fa-retweet others-retweet"></i><?php echo h($whoRetweet['name']); ?>さんがリツイート</p>
					<?php endif; ?>
					<!-- / リツイートをした人の表示 -->

					<img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
					<p><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
					<p class="day">
						<a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
						<?php if ($post['reply_post_id'] > 0) : ?>
							<a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
						<?php endif; ?>
						<?php if ($_SESSION['id'] == $post['member_id']) : ?>
							[<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
						<?php endif; ?>
					</p>

					<div class="retweetAndLike">
						<!-- リツイートの表示 -->
						<div class="retweet">
							<?php
							$isRetweets = $db->prepare('SELECT member_id FROM posts WHERE retweet_post_id IN(?,?) AND retweet_member_id=?');
							$isRetweets->execute([$post['retweet_post_id'], $post['id'], $_SESSION['id']]);
							$isRetweet = $isRetweets->fetch();
							?>
							<?php if ($isRetweet['member_id'] === $_SESSION['id']) : ?>
								<a href="retweet.php?id=<?php echo h($post['id']); ?>&option=dis"><i class="fas fa-retweet button-retweeted"></i></a>
							<?php else : ?>
								<a href="retweet.php?id=<?php echo h($post['id']); ?>&option=on"><i class="fas fa-retweet button-retweet"></i></a>
							<?php endif; ?>
							<?php
							// リツイート数を取得する
							$retweetCounts = $db->prepare('SELECT COUNT(retweet_post_id) AS retweetCount FROM posts WHERE retweet_post_id IN (?,?) AND retweet_post_id > 0');
							$retweetCounts->execute([$post['retweet_post_id'], $post['id']]);
							$retweetCount = $retweetCounts->fetch();
							if ($retweetCount['retweetCount'] !== '0') {
								echo '<span class="count-retweeted">' . $retweetCount['retweetCount'] . '</span>';
							}
							?>
						</div>
						<!-- / リツイートの表示 -->

						<!-- いいねの表示 -->
						<div class="like">
							<?php
							// いいね済かどうかを判定する
							$isLikes = $db->prepare('SELECT l.member_id FROM posts p, likes l WHERE (l.post_id=p.retweet_post_id AND p.id=?) OR l.post_id=p.id AND p.retweet_post_id IN (?,?) AND l.member_id=? AND (p.retweet_post_id<>0 OR l.post_id=?)');
							$isLikes->execute([$post['id'], $post['retweet_post_id'], $post['id'], $_SESSION['id'], $post['id']]);
							$isLike = $isLikes->fetch();
							?>
							<?php if ($isLike['member_id'] === $_SESSION['id']) : ?>
								<a href="like.php?id=<?php echo h($post['id']); ?>&option=dis"><i class="fas fa-heart button-liked"></i></a>
							<?php else : ?>
								<a href="like.php?id=<?php echo h($post['id']); ?>"><i class="far fa-heart button-like"></i></a>
							<?php endif; ?>
							<?php
							// いいねの数を取得する
							// 参考先のサイト: https://stackoverflow.com/questions/17371639/how-to-store-arrays-in-mysql
							$likeCounts = $db->prepare('SELECT COUNT(l.member_id) AS likeCount FROM posts p, likes l WHERE (l.post_id=p.retweet_post_id AND p.id=?) OR l.post_id=p.id AND p.retweet_post_id IN (?,?) AND (p.retweet_post_id<>0 OR l.post_id=?)');
							$likeCounts->execute([$post['id'], $post['retweet_post_id'], $post['id'], $post['id']]);
							$likeCount = $likeCounts->fetch();
							if ($likeCount['likeCount'] !== '0') {
								echo '<span class="count-liked">' . $likeCount['likeCount'] . '</span>';
							}
							?>
						</div>
						<!-- / いいねの表示 -->
					</div>
				</div>
			<?php
			endforeach;
			?>

			<ul class="paging">
				<?php if ($page > 1) { ?>
					<li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
				<?php } else { ?>
					<li>前のページへ</li>
				<?php } ?>
				<?php if ($page < $maxPage) { ?>
					<li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
				<?php } else { ?>
					<li>次のページへ</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</body>

</html>
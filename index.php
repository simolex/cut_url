<?php
require_once "includes/functions.php";

if (isset($_GET['url']) && !empty($_GET['url'])) {
	$url = strtolower(trim($_GET['url']));

	$link = getLinkInfo_ByShortLink($url);

	if (empty($link)) {
		header("Location: 404.php");
		die;
	}
	incrementViewLink($url);
	header('Location: ' . $link['orginal_link']);
	die;
}

require "includes/header.php";
?>
<main class="container">
	<?php if (!isset($_SESSION['user']['id'])) { ?>
		<div class="row mt-5">
			<div class="col">
				<h2 class="text-center">Необходимо <a href="<?php echo get_url("register.php"); ?>">зарегистрироваться</a> или <a href="<?php echo get_url("login.php"); ?>">войти</a> под своей учетной записью</h2>
			</div>
		</div>
	<?php } ?>
	<div class="row mt-5">
		<div class="col">
			<h2 class="text-center">Пользователей в системе: <?php echo $countUser; ?></h2>
		</div>
	</div>
	<div class="row mt-5">
		<div class="col">
			<h2 class="text-center">Ссылок в системе: <?php echo $countLinks; ?></h2>
		</div>
	</div>
	<div class="row mt-5">
		<div class="col">
			<h2 class="text-center">Всего переходов по ссылкам: <?php echo $countViews; ?></h2>
		</div>
	</div>
</main>

<?php require "includes/footer.php"; ?>
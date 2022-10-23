<?php require_once "common.php";
require_once "pages.php";
$hTitle     = TITLE_DEFAULT;
$hHead      = function () {
};
$hBodyEnd   = function () {
};
$hShowLinks = function ($currPageIdx) {
	global $pages;
	foreach ($pages as $idx => $page) {
		if (!$page->showsOnHeader) continue;
		if ($idx === $currPageIdx) { ?>
			<li><span class="link-like disabled"><?= h($page->name) ?></span></li>
		<?php } else { ?>
			<li><a href="<?= $page->path ?>"><?= h($page->name) ?></a></li>
		<?php } ?>
	<?php }
};

function getStoredErrors(): array {
	if (isset($_SESSION["errors"]) && is_array($_SESSION["errors"]) && count($_SESSION["errors"]) > 0) {
		$ret                = $_SESSION["errors"];
		$_SESSION["errors"] = [];
		return $ret;
	}
	return [];
}

function storeErrors($errors) {
	$_SESSION["errors"] = $errors;
}

function getStoredMessages(): array {
	if (isset($_SESSION["messages"]) && is_array($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) {
		$ret                 = $_SESSION["messages"];
		$_SESSION["messages"] = [];
		return $ret;
	}
	return [];
}

function storeMessages($messages) {
	$_SESSION["messages"] = $messages;
}

function switchLocation($loc) {
	global $errors, $messages;
	storeErrors($errors);
	storeMessages($messages);
	header("Location: $loc");
	die();
}

$errors = getStoredErrors();
function errStr($e){
	global $errors;
	$errors[] = $e;
}

$messages = getStoredMessages();
function msgStr($m){
	global $messages;
	$messages[] = $m;
}


function basicSetup(bool $restoreUserId = true){
	global $pages;
	if($restoreUserId) {
		if (!restoreUserId()) {
			$errors = [];
			errStr("You haven't signed in yet.");
			switchLocation($pages[PageIndex::SignIn]->path);
			die();
		}
	}
}

function show_html_start_block($currPageIdx = PageIndex::None) {
	global $hTitle, $hHead, $hShowLinks;
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<title><?= $hTitle ?></title>
		<link href="styles/styles.css" rel="stylesheet" type="text/css" />
		<style>
		html {
			height: 100%;
		}

		body {
			height: 100%;
			overflow: hidden;
			display: flex;
			flex-direction: column;
		}

		#container-container {
			flex-grow: 1;
			overflow-y: auto;
		}

		#main-container {
			margin-top: 1em;
		}
		</style>
		<?php $hHead(); ?>
	</head>
	<body>
	<div class="header">
		<span class="heading"><?= TITLE_DEFAULT ?></span>
		<ul class="link-list">
			<?php $hShowLinks($currPageIdx); ?>
		</ul>
	</div>
	<div id="container-container">
	<div id="main-container" class="container">
<?php }

function show_html_end_block() {
	global $hBodyEnd; ?>
	</div>
	</div>
	<?php $hBodyEnd(); ?>
	</body>
	</html>
<?php }

function show_messages() {
	global $errors, $messages;
	if (count($errors) > 0) { ?>
		<div class="error-list">
			<h5>Error(s):</h5>
			<?php foreach ($errors as $error) { ?>
				<?= $error ?><br />
			<?php } ?>
		</div>
	<?php }
	if (count($messages) > 0) { ?>
		<div class="message-list">
			<h5>Message(s):</h5>
			<?php foreach ($messages as $message) { ?>
				<?= $message ?><br />
			<?php } ?>
		</div>
	<?php }
}

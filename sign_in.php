<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(false);

$email = "";

if(isset($_SESSION[USER_ID]) || isset($_COOKIE[USER_ID])){
	msgStr("Already signed in.");
	switchLocation($pages[PageIndex::Home]->path);
}

if (isset($_POST["submit"])) {
	if(!isset($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
		errStr("Invalid email");
	}

	if(!isset($_POST["password"])){
		errStr("No password specified");
	}

	if(count($errors) > 0){
		goto endPostChecking;
	}

	$res = signIn($_POST["email"], $_POST["password"]);
	if($res !== true){
		errStr($res);
		goto endPostChecking;
	} else {
		msgStr("Signed in sucessfully.");
		switchLocation($pages[PageIndex::Home]->path);
	}
}

endPostChecking:

$hHead = function () { ?>
	<link href="styles/sign_in_up.css" rel="stylesheet" type="text/css" />
<?php };

$hBodyEnd = function () { ?>
	<script src="js/common.js"></script>
<?php };

show_html_start_block(PageIndex::SignIn, false); ?>
	<h1 class="text-center">My Muesli Mixes</h1>
<?php show_messages() ?>
	<form action="#" method="post" class="sign-form w-fit-content mx-auto">
		<div class="flex">
			<label class="form-label my-auto" for="email">Email</label>
			<input id="email" class="form-input" name="email" required type="email" value="<?= $email ?>" />
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="password">Password</label>
			<input id="password" class="form-input" name="password" required type="password" />
		</div>
		<button class="s3 mx-auto" type="submit" name="submit" value="1">Sign In</button>
		<div class="text-center">
			<a href="<?= $pages[PageIndex::SignUp]->path ?>">
				Don't have an account? Sign Up Instead?
			</a>
		</div>
	</form>
<?php show_html_end_block();

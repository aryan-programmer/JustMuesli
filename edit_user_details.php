<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);

$userDetails = getUserDetails();

$address = "";
$zip     = "";
$city    = "";
$country = COUNTRIES[0];
$phone   = "";
$name    = "";

if (!isset($userDetails)) {
	$userDetails = [];
	errStr("Failed to get user details from the database");
} else {
	$address = $userDetails->address;
	$zip     = $userDetails->zip;
	$city    = $userDetails->city;
	$country = $userDetails->country;
	$phone   = $userDetails->phone;
	$name    = $userDetails->name;
}


if (isset($_POST["submit"])) {
	if (!isset($_POST["address"])) {
		errStr("Please enter an address");
	} else {
		$address = $_POST["address"];
	}
	if (!isset($_POST["zip"])) {
		errStr("Please enter a zip code");
	} else {
		$zip = $_POST["zip"];
	}
	if (!isset($_POST["city"])) {
		errStr("Please enter a city");
	} else {
		$city = $_POST["city"];
	}
	if (!isset($_POST["country"])) {
		errStr("Please enter a country");
	} elseif (!in_array($_POST["country"], COUNTRIES)) {
		errStr("Please enter a valid country");
	} else {
		$country = $_POST["country"];
	}
	if (!isset($_POST["phone"])) {
		errStr("Please enter a phone number");
	} else {
		$phone = $_POST["phone"];
	}
	if (!isset($_POST["name"])) {
		errStr("Please enter a name");
	} else {
		$name = $_POST["name"];
	}

	if (strlen($address) < 5) {
		errStr("The address must be atleast 5 characters long");
	}
	if (strlen($name) < 5) {
		errStr("The name must be atleast 5 characters long");
	}
	if (strlen($city) < 5) {
		errStr("The city name must be atleast 5 characters long");
	}
	if (preg_match_all("/[0-9]/", $zip) < 4) {
		errStr("The zip code must be atleast 4 numeric characters long");
	}

	if (preg_match("/[^0-9\-\s]/", $zip)) {
		errStr("The zip code must contain only numeric characters, dashes and spaces");
	}
	if (!preg_match(PHONE_REGEX, $phone)) {
		errStr("The phone number should be of the form 1234567890, 123 456 7890, or 123-456-7890");
	}

	if (count($errors) > 0) {
		goto endPostChecking;
	}
	
	$res = updateUserDetails($address,$zip,$city,$country,$phone,$name);
	if(!$res){
		errStr("Failed to update user details");
		goto endPostChecking;
	} else {
		msgStr("Successfully updated user details");
		switchLocation($pages[PageIndex::Home]->path);
	}
}

endPostChecking:

$hHead = function () { ?>
	<link href="styles/edit_user_details.css" rel="stylesheet" type="text/css" />
<?php };

$hBodyEnd = function () { ?>
	<script src="js/common.js"></script>
<?php };

show_html_start_block(PageIndex::EditUserDetails); ?>
	<h1 class="text-center">Edit User Details</h1>
<?php show_messages() ?>
	<form action="#" method="post" class="form w-fit-content mx-auto">
		<div class="flex">
			<label class="form-label my-auto" for="name">Name</label>
			<input id="name" class="form-input" name="name" required minlength="5" value="<?= h($name) ?>" />
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="address">Address</label>
			<textarea
				id="address" class="form-input" name="address" required minlength="5"><?= h($address) ?></textarea>
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="zip">Zip</label>
			<input id="zip" class="form-input" name="zip" required type="text" minlength="4" value="<?= h($zip) ?>" />
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="city">City</label>
			<input id="city" class="form-input" name="city" required minlength="5" value="<?= h($city) ?>" />
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="country">Country</label>
			<select class="form-input" id="country" name="country">
				<?php foreach (COUNTRIES as $ctry) {
					$selected = $ctry === $country ? "selected" : ""; ?>
					<option value="<?= $ctry ?>" <?= $selected ?>><?= $ctry ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="flex">
			<label class="form-label my-auto" for="phone">Phone</label>
			<input id="phone" class="form-input" name="phone" required type="tel" value="<?= h($phone) ?>" />
		</div>
		<button class="s3 mx-auto" type="submit" name="submit" value="1">Save</button>
	</form>
<?php show_html_end_block();

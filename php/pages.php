<?php
const TITLE_DEFAULT = "Just Muesli";
abstract class PageIndex {
	const None            = -1;
	const Home            = self::None + 1;
	const Mixer           = self::Home + 1;
	const Mixes           = self::Mixer + 1;
	const Order           = self::Mixes + 1;
	const EditUserDetails = self::Order + 1;
	const SignOut         = self::EditUserDetails + 1;
	const SignIn          = self::SignOut + 1;
	const SignUp          = self::SignIn + 1;
	const MixDetails      = self::SignUp + 1;
	const DeleteMix       = self::MixDetails + 1;

	private function __construct() {

	}
}

class Page {
	public $path;
	public $name;
	public $showsOnHeader;

	public function __construct($path,
	                            $name,
	                            $showsOnHeader) {
		$this->path          = (string)$path;
		$this->name          = (string)$name;
		$this->showsOnHeader = $showsOnHeader === true;
	}

	public function withId($id): string {
		$ids = urlencode((string)$id);
		return "$this->path?id=$ids";
	}
}

$pages = [
	PageIndex::Home            => new Page(
		"index.php",
		"Home",
		true
	),
	PageIndex::Mixer           => new Page(
		"mixer.php",
		"Mix",
		true
	),
	PageIndex::Mixes           => new Page(
		"mixes.php",
		"Show Mixes",
		true
	),
	PageIndex::Order           => new Page(
		"order.php",
		"Order",
		true
	),
	PageIndex::EditUserDetails => new Page(
		"edit_user_details.php",
		"Edit User Details",
		true
	),
	PageIndex::SignOut         => new Page(
		"sign_out.php",
		"Sign Out",
		true
	),
	PageIndex::SignIn          => new Page(
		"sign_in.php",
		"Sign In",
		false
	),
	PageIndex::SignUp          => new Page(
		"sign_up.php",
		"Sign Up",
		false
	),
	PageIndex::MixDetails      => new Page(
		"mix_details.php",
		"Show Mix Details",
		false
	),
	PageIndex::DeleteMix       => new Page(
		"delete_mix.php",
		"Delete Mix",
		false
	),
];

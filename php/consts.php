<?php
const MAX_GRAMS = 600;

const COUNTRIES = [
	"India",
	"Switzerland",
	"USA"
];

const USER_ID = "USER_ID";
const USER_ID_SAVE_COOKIE_FROM_SESS = "USER_ID_SAVE_COOKIE_FROM_SESS";

const PHONE_REGEX = /** @lang PhpRegExp */
'/^[0-9]{3}([-\s]?)[0-9]{3}([-\s]?)[0-9]{4}$/';

function pvar_dump(...$v) {
	echo '<pre style="max-height: 20em; overflow-y: scroll;">';
	var_dump(...$v);
	echo '</pre>';
}

function h($v): string {
	return htmlentities($v, ENT_QUOTES | ENT_SUBSTITUTE);
}

function rnd($v): float {
	return round((double)$v, 2);
}

function rndStr($v): string {
	return number_format((float)$v, 2, '.', '');
}

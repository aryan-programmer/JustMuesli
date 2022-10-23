<?php require_once "php/common.php";
require_once "php/pages.php";
basicSetup(true);

if (isset($_GET["id"])) {
	$sid = $_GET["id"];
	$id  = (int)$sid;
	if (is_nan($id)) {
		errStr("Invalid mix id $sid");
		goto endChecking;
	}
	$res = deleteMixById($id);
	if ($res !== true) {
		errStr($res);
		goto endChecking;
	}
} else {
	errStr("No mix id specified");
	goto endChecking;
}

endChecking:
storeErrors($errors);
switchLocation($pages[PageIndex::Mixes]->path);

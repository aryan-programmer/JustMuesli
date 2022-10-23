<?php require_once "php/common.php";
require_once "php/templates.php";

basicSetup(true);
unsetUserId();
msgStr("Signed out successfully.");
switchLocation($pages[PageIndex::SignIn]->path);


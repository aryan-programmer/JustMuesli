<?php require_once "php/common.php";

header('Content-Type: application/json');
echo json_encode(getIngredients());

<?php session_start();
require_once "consts.php";
require_once "classes.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "just_muesli");
if ($conn->connect_errno != 0) {
	die("Failed to connect");
}

if (isset($_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS]) && $_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS] === true) {
	unset($_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS]);
	setcookie(USER_ID, $_SESSION[USER_ID], time() + 60 * 60 * 24 * 365);
}

$userId = null;

function saveUserId($uid) {
	$_SESSION[USER_ID]                       = $uid;
	$_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS] = true;
}

function restoreUserId(): bool {
	global $userId;
	if (isset($_SESSION[USER_ID])) {
		$userId = $_SESSION[USER_ID];
		return true;
	} elseif (isset($_COOKIE[USER_ID])) {
		$_SESSION[USER_ID] = $_COOKIE[USER_ID];
		$userId            = $_COOKIE[USER_ID];
		return true;
	}
	return false;
}

function unsetUserId() {
	global $userId;
	$userId            = null;
	$_SESSION[USER_ID] = null;
	$_COOKIE[USER_ID]  = null;
	unset($_SESSION[USER_ID]);
	unset($_COOKIE[USER_ID]);
	setcookie(USER_ID, null, time() - 1);
}

function signIn($email, $password) {
	global $conn, $userId;

	try {
		$stmt = $conn->prepare("SELECT id, email, password
FROM users
WHERE email = ?;");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$res   = $stmt->get_result();
		$assoc = $res->fetch_all(MYSQLI_ASSOC);
		if (count($assoc) < 1) {
			return "Email not used to sign up a user";
		}
		$dbPwdHash = $assoc[0]["password"];
		if (!password_verify($password, $dbPwdHash)) {
			return "Invalid password";
		}
		$userId = (int)$assoc[0]["id"];
		saveUserId($userId);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function signUp($email, $password) {
	global $conn, $userId;

	try {
		$pwdHash = password_hash($password, PASSWORD_DEFAULT);
		$stmt    = $conn->prepare("INSERT INTO users (email, password)
	VALUE (?, ?);");
		$stmt->bind_param("ss", $email, $pwdHash);
		$stmt->execute();
		$userId = $conn->insert_id;
		saveUserId($userId);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function getIngredients(): array {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT i.id,
       i.name,
       category_id,
       ic.name AS category_name,
       cost_per_600g,
       kcal_carbs_per_100g,
       kcal_proteins_per_100g,
       kcal_fats_per_100g,
       kcal_per_100g
FROM ingredients i
	JOIN ingredient_categories ic ON i.category_id = ic.id
ORDER BY category_id, i.name;");
		$stmt->execute();
		$stmt->bind_result($id,
			$name,
			$categoryId,
			$categoryName,
			$costPer600g,
			$kcalPer100g,
			$kcalCarbsPer100g,
			$kcalProteinsPer100g,
			$kcalFatsPer100g);
		$res = [];
		while ($stmt->fetch()) {
			$res[] = new Ingredient($id,
				$name,
				$categoryId,
				$categoryName,
				$costPer600g,
				$kcalPer100g,
				$kcalCarbsPer100g,
				$kcalProteinsPer100g,
				$kcalFatsPer100g);
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return [];
	}
}

function getMixIngredientsOfMixById($id) {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT i.id    AS ing_id,
       i.name,
       i.category_id,
       ic.name AS category_name,
       i.cost_per_600g,
       i.kcal_per_100g,
       i.kcal_carbs_per_100g,
       i.kcal_proteins_per_100g,
       i.kcal_fats_per_100g,
       mil.is_filler,
       mil.grams
FROM mix_ingredients_list mil
	JOIN mixes m ON m.id = mil.mix_id
	JOIN ingredients i ON i.id = mil.ingredient_id
	JOIN ingredient_categories ic ON ic.id = i.category_id
WHERE m.id = ?
ORDER BY mil.grams DESC;");
		$stmt->bind_param("i", $id);
		$stmt->bind_result($ingId,
			$name,
			$categoryId,
			$categoryName,
			$costPer600g,
			$kcalPer100g,
			$kcalCarbsPer100g,
			$kcalProteinsPer100g,
			$kcalFatsPer100g,
			$isFiller,
			$grams);
		$stmt->execute();
		$res = [];
		while ($stmt->fetch()) {
			$mixIng = new MixIngredient(
				new Ingredient($ingId,
					$name,
					$categoryId,
					$categoryName,
					$costPer600g,
					$kcalPer100g,
					$kcalCarbsPer100g,
					$kcalProteinsPer100g,
					$kcalFatsPer100g),
				$grams,
				$isFiller
			);
			if ($isFiller) {
				array_unshift($res, $mixIng);
			} else {
				$res[] = $mixIng;
			}
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		pvar_dump($ex);
		return null;
	}
}

function getMixById($id) {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT id, name, user_id
FROM mixes
WHERE id = ?;");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res   = $stmt->get_result();
		$assoc = $res->fetch_all(MYSQLI_ASSOC);
		if (count($assoc) < 1) {
			return null;
		}
		$name    = $assoc[0]["name"];
		$mixIngs = getMixIngredientsOfMixById($id);
		if (!isset($mixIngs) || count($mixIngs) == 0) {
			return null;
		}
		return new Mix($id, $name, $mixIngs);
	} catch (mysqli_sql_exception $ex) {
		return null;
	}
}

function getMixNamesAssocById() {
	global $conn, $userId;

	try {
		$stmt = $conn->prepare("SELECT id, name, user_id
FROM mixes
WHERE user_id = ?
ORDER BY name;");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($id, $name, $userId);
		$res = [];
		while ($stmt->fetch()) {
			$res[$id] = $name;
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return null;
	}
}


function getMixesAssocById(): array {
	$res = getMixNamesAssocById();

	foreach ($res as $id => $name) {
		$mixIngs  = getMixIngredientsOfMixById($id);
		$res[$id] = new Mix($id, $name, $mixIngs);
	}

	return $res;
}

function getUserDetails() {
	global $conn, $userId;
	try {
		$stmt = $conn->prepare("SELECT address,
       zip,
       city,
       country,
       phone,
       name
FROM users
WHERE id = ?;");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($address,$zip,$city,$country,$phone,$name);
		if($stmt->fetch()){
			return new UserDetails($address,$zip,$city,$country,$phone,$name);
		}
		return null;
	}catch(mysqli_sql_exception $ex){
		return null;
	}
}

function updateUserDetails($address,$zip,$city,$country,$phone,$name){
	global $conn, $userId;
	try {
		$stmt = $conn->prepare("UPDATE users
SET address = ?,
    zip     = ?,
    city    = ?,
    country = ?,
    phone   = ?,
    name    = ?
WHERE id = ?;");
		$stmt->bind_param("ssssssi", $address,$zip,$city,$country,$phone,$name,$userId);
		$stmt->execute();
		if ($stmt->affected_rows < 1){
			return false;
		}
		return true;
	}catch(mysqli_sql_exception $ex){
		return false;
	}
}

function addMix($mixName, $mixIngredients) {
	global $conn, $userId;

	$conn->begin_transaction();

	if (count($mixIngredients) < 1) {
		return "A mix must contain at least a filler.";
	}

	try {
		$stmt = $conn->prepare("INSERT INTO mixes (name, user_id)
	VALUE (?, ?);");
		$stmt->bind_param("si", $mixName, $userId);
		$stmt->execute();

		$mixId = $conn->insert_id;

		$stmt = $conn->prepare("INSERT INTO mix_ingredients_list (is_filler, mix_id, ingredient_id, grams)
	VALUE (?, ?, ?, ?);");
		$stmt->bind_param("iiid", $isFiller, $mixId, $ingredientId, $grams);
		foreach ($mixIngredients as $mixIngredient) {
			$isFiller     = $mixIngredient->isFiller ? 1 : 0;
			$ingredientId = (int)$mixIngredient->ingredient->id;
			$grams        = (double)$mixIngredient->grams;
			$stmt->execute();
		}

		$conn->commit();

		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

function placeOrder($order) {
	global $conn, $userId;

	$mixOrderLength = count($order->mixOrders);

	$conn->begin_transaction();

	try {
		$stmt = $conn->prepare("INSERT INTO orders (user_id, mixes_num, cost_init, cost_shipping, cost_taxes)
	VALUE (?, ?, ?, ?, ?);");
		$stmt->bind_param("iiddd",
			$userId,
			$mixOrderLength,
			$order->costInit,
			$order->costShipping,
			$order->costTaxes);
		$stmt->execute();

		$orderId = $stmt->insert_id;

		$stmt = $conn->prepare("INSERT INTO order_mixes_list (mix_id, order_id, quantity, isXXL)
	VALUE (?, ?, ?, ?);");
		$stmt->bind_param("iiii", $mixId, $orderId, $quantity, $isXXL);
		foreach ($order->mixOrders as $mixOrder) {
			$mixId    = $mixOrder->mixId;
			$quantity = $mixOrder->quantity;
			$isXXL    = $mixOrder->isXXL;
			$stmt->execute();
		}

		$conn->commit();

		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

function deleteMixById($id) {
	global $conn, $userId;

	$conn->begin_transaction();
	try {
		$stmt = $conn->prepare("DELETE
FROM mixes
WHERE id = ?
  AND user_id = ?;");
		$stmt->bind_param("ii", $id, $userId);
		$stmt->execute();

		if ($stmt->affected_rows < 1) {
			$conn->rollback();
			return "The user doesn't own a mix with the id $id";
		}

		$conn->commit();

		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

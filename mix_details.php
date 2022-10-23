<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);

$mix = null;

if (isset($_GET["id"])) {
	$mix = getMixById($_GET["id"]);
	if ($mix == null) {
		errStr("Failed to get mix from database");
	}
} elseif (isset($_POST["details"])) {
	$ings = getIngredients();

	if (!isset($ings) || count($ings) == 0) {
		errStr("No muesli ingredients found");
	}

	if (!(isset($_POST["name"]) && is_string($_POST["name"]) && $_POST["name"] !== "")) {
		errStr("Please enter a name for the mix");
	}

	if (!(isset($_POST["ingredients"]) && is_array($_POST["ingredients"]))) {
		errStr("There is no ingredients list");
	}

	if (count($errors) != 0) {
		goto endPostChecking;
	}

	$name = $_POST["name"];

	$ingsById = [];
	foreach ($ings as $ingredient) {
		$ingsById[$ingredient->id] = $ingredient;
	}
	$mixIngredients = [];
	foreach ($_POST["ingredients"] as $id => $v) {
		$isFiller = false;
		if (array_key_exists("isFiller", $v)) {
			$isFiller = true;
		}
		$mixIng = new MixIngredient($ingsById[$id], $v["grams"], $isFiller);
		if ($isFiller) {
			array_unshift($mixIngredients, $mixIng);
		} else {
			$mixIngredients[] = $mixIng;
		}
	}

	$mix = new Mix(-1, $name, $mixIngredients);
} else {
	errStr("Invalid parameters");
}

endPostChecking:
show_html_start_block(PageIndex::Home); ?>
	<h1 class="text-center">Muesli Mix Details</h1>
<?php show_messages();
if (count($errors) == 0) { ?>
	<h3 class="text-center">Mix: <?= $mix->name ?></h3>
	<div class="w-fit-content mx-auto">
		<?php
		$kcalPer100Gram         = $mix->kcal / MAX_GRAMS * 100;
		$kcalCarbsPer100Gram    = $mix->kcalCarbs / MAX_GRAMS * 100;
		$kcalProteinsPer100Gram = $mix->kcalProteins / MAX_GRAMS * 100;
		$kcalFatsPer100Gram     = $mix->kcalFats / MAX_GRAMS * 100;
		$kjPer100Gram           = $kcalPer100Gram * 4.184 /*kcal/g to kj/g*/
		;
		$kjCarbsPer100Gram      = $kcalCarbsPer100Gram * 4.184 /*kcal/g to kj/g*/
		;
		$kjProteinsPer100Gram   = $kcalProteinsPer100Gram * 4.184 /*kcal/g to kj/g*/
		;
		$kjFatsPer100Gram       = $kcalFatsPer100Gram * 4.184 /*kcal/g to kj/g*/
		; ?>
		<table class="table w-auto my-2rem mx-auto">
			<caption>Nutrient Details</caption>
			<tr>
				<th>Nutrient</th>
				<th>Kcal per 100 grams</th>
				<th>KJ per 100 grams</th>
			</tr>
			<tr class="highlight">
				<th>Total</th>
				<td class="number-align"><?= rndStr($kcalPer100Gram) ?></td>
				<td class="number-align"><?= rndStr($kjPer100Gram) ?></td>
			</tr>
			<tr>
				<th>Carbohydrates</th>
				<td class="number-align"><?= rndStr($kcalCarbsPer100Gram) ?></td>
				<td class="number-align"><?= rndStr($kjCarbsPer100Gram) ?></td>
			</tr>
			<tr>
				<th>Proteins</th>
				<td class="number-align"><?= rndStr($kcalProteinsPer100Gram) ?></td>
				<td class="number-align"><?= rndStr($kjProteinsPer100Gram) ?></td>
			</tr>
			<tr>
				<th>Fats</th>
				<td class="number-align"><?= rndStr($kcalFatsPer100Gram) ?></td>
				<td class="number-align"><?= rndStr($kjFatsPer100Gram) ?></td>
			</tr>
		</table>
		<table class="table mx-auto">
			<caption>Mix details</caption>
			<tr>
				<th>Ingredient</th>
				<th>Grams</th>
				<th>Cost</th>
				<th>Is Filler?</th>
				<th>Kcals</th>
				<th>Kcals in Carbohydrates</th>
				<th>Kcals in Proteins</th>
				<th>Kcals in Fats</th>
			</tr>
			<tr class="highlight">
				<td>Total</td>
				<td class="number-align"><?= rndStr(MAX_GRAMS) ?></td>
				<td class="number-align"><?= rndStr($mix->cost) ?></td>
				<td>N/A</td>
				<td class="number-align"><?= rndStr($mix->kcal) ?></td>
				<td class="number-align"><?= rndStr($mix->kcalCarbs) ?></td>
				<td class="number-align"><?= rndStr($mix->kcalProteins) ?></td>
				<td class="number-align"><?= rndStr($mix->kcalFats) ?></td>
			</tr>
			<?php foreach ($mix->mixIngredients as $mixIng) { ?>
				<tr>
					<td><?= $mixIng->ingredient->name ?></td>
					<td class="number-align"><?= rndStr($mixIng->grams) ?></td>
					<td class="number-align"><?= rndStr($mixIng->cost) ?></td>
					<td><?= $mixIng->isFiller === true ? "Yes" : "No" ?></td>
					<td class="number-align"><?= rndStr($mixIng->kcal) ?></td>
					<td class="number-align"><?= rndStr($mixIng->kcalCarbs) ?></td>
					<td class="number-align"><?= rndStr($mixIng->kcalProteins) ?></td>
					<td class="number-align"><?= rndStr($mixIng->kcalFats) ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php } ?>
<?php show_html_end_block();

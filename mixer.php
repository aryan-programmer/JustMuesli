<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);

$ings = getIngredients();

if (!isset($ings)) $ings = [];
if (count($ings) == 0) {
	errStr("Failed to ingredients list from the database");
}

if (isset($_POST["submit"])) {
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
		$mixIngredients[] = new DbInsertMixIngredient($ingsById[$id], $v["grams"], $isFiller);
	}

	$addError = addMix($name, $mixIngredients);
	if ($addError !== true && is_string($addError)) {
		errStr($addError);
		goto endPostChecking;
	}

	header("Location: ./index.php");
}

endPostChecking:
$ingsByCat = [];
foreach ($ings as $ingredient) {
	$hsCategoryName = $ingredient->htmlSafeCategoryName;
	if (!array_key_exists($hsCategoryName, $ingsByCat)) {
		$ingsByCat[$hsCategoryName] = [];
	}
	$ingsByCat[$hsCategoryName][] = $ingredient;
}

$hHead = function () { ?>
	<link href="styles/mixer.css" rel="stylesheet" type="text/css" />
<?php };

$hBodyEnd = function () {
	global $ings; ?>
	<script src="js/common.js"></script>
	<script src="js/mixer.js"></script>
	<script>
	let ings = JSON.parse(atob('<?= base64_encode(json_encode($ings)) ?>'));

	htmlSetup(ings);
	</script>
<?php };

show_html_start_block(PageIndex::Mixer); ?>
	<h1 class="text-center">Muesli Mixer</h1>
<?php show_messages() ?>
	<div class="mixer">
		<div class="mixer-ingredients-selector">
			<div class="mixer-category-list">
				<div>
					<?php foreach ($ingsByCat as $hsCategory => $ingredients) { ?>
						<a
							class="h5"
							href="#category-<?= (int)$ingredients[0]->categoryId ?>"><?= $hsCategory ?></a>
						<br />
					<?php } ?>
				</div>
			</div>
			<div class="mixer-ing-sel-list">
				<?php
				foreach ($ingsByCat as $hsCategory => $ingredients) {
					?>
					<span id="category-<?= (int)$ingredients[0]->categoryId ?>"
					      class="h3"><?= $hsCategory ?></span><?php
					foreach ($ingredients as $ingredient) {
						?>
						<form class="mixer-selector-ingredient">
						<button type="submit" class="button mixer-selector-button">Add</button>
						<p class="h4"><?= h($ingredient->name) ?></p>
						<?= h($ingredient->costPer600g) ?> Rs per 600 g
						<input
							class="m-s-i-hidden-id"
							type="hidden"
							name="id"
							value="<?= h($ingredient->id) ?>" />
						</form><?php
					} ?>
					<hr /><?php
				}
				?>
			</div>
		</div>
		<div class="mixer-mix">
			<form class="w-100 flex fl-col h-100 my-0" action="#" method="post">
				<div class="flex">
					<label class="fl-g-2 form-input" for="name">Name</label>
					<input class="fl-g-7" id="name" type="text" name="name" required>
				</div>
				<ul id="mix-ingredients" class="fl-g-1">
				</ul>
				<div class="flex">
					<div class="fl-g-1">
						<span id="mix-cost-span">000.00</span> Rs / 600 g<br />
						<span id="mix-kcal-span">000.00</span> Kcal / 100 g<br />
					</div>
					<button
						id="mix-details-btn"
						class="fl-g-1"
						name="details"
						value="1"
						type="submit"
						formaction="<?= $pages[PageIndex::MixDetails]->path ?>"
						formtarget="_blank">Details
					</button>
				</div>
				<button class="h5" type="submit" name="submit" value="1">Save Muesli</button>
			</form>
		</div>
	</div>
<?php show_html_end_block();

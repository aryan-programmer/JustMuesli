<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);

$mixes = getMixNamesAssocById();

if (!isset($mixes)) {
	$mixes = [];
	errStr("Failed to get mix list from the database");
}

$hHead = function () { ?>
	<link href="styles/mixes.css" rel="stylesheet" type="text/css" />
<?php };

$hBodyEnd = function () { ?>
	<script src="js/common.js"></script>
	<script src="js/mixes.js"></script>
	<script>
	htmlSetup();
	</script>
<?php };

show_html_start_block(PageIndex::Mixes); ?>
	<h1 class="text-center">My Muesli Mixes</h1>
<?php show_messages() ?>
	<div class="mixes w-fit-content mx-auto">
		<?php
		$mixDetailsPage = $pages[PageIndex::MixDetails];
		foreach ($mixes as $mixId => $mixName) {
			$hMixName = h($mixName); ?>
			<div class="mix">
				<span class="mix-name"><?= $hMixName ?></span>
				<div class="mix-actions">
					<a
						class="button mix-action" href="<?= $mixDetailsPage->withId($mixId) ?>"
						target="_blank">Details</a>
					<button
						class="err mix-action mix-action-delete" data-mix-id="<?= h($mixId) ?>"
						data-mix-name="<?= $hMixName ?>">Delete
					</button>
				</div>
			</div>
		<?php } ?>
	</div>
<?php show_html_end_block();

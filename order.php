<?php require_once "php/common.php";
require_once "php/templates.php";
basicSetup(true);

$mixes = getMixesAssocById();

if (!isset($mixes)) {
	$mixes    = [];
	errStr("Failed to get mix list from the database");
	goto endPostChecking;
}

if (isset($_POST["submit"])) {
	if (!isset($_POST["costInit"]) ||
		!isset($_POST["costShipping"]) ||
		!isset($_POST["costTaxes"])) {
		errStr("Cost value(s) not specified");
		goto endPostChecking;
	}

	$costInit     = (double)$_POST["costInit"];
	$costShipping = (double)$_POST["costShipping"];
	$costTaxes    = (double)$_POST["costTaxes"];

	if (is_nan($costInit) ||
		is_nan($costShipping) ||
		is_nan($costTaxes)) {
		errStr("Cost value(s) invalid");
		goto endPostChecking;
	}

	$mixOrders = [];
	foreach ($_POST["orders"] as $sMixId => $r) {
		if (!isset($r["quantity"])) continue;
		if ($r["quantity"] === "0") continue;
		$mixId       = (int)$sMixId;
		$quantity    = (int)$r["quantity"];
		$isXXL       = isset($r["isXXL"]) && $r["isXXL"] === "1";
		$mixOrders[] = new DbMixOrderRow($mixId, $quantity, $isXXL);
	}

	$order = new DbInsertOrder($mixOrders, $costInit, $costShipping, $costTaxes);

	$res = placeOrder($order);
	if ($res !== true) {
		errStr($res);
	}
}

endPostChecking:

$hHead = function () { ?>
	<link href="styles/order.css" rel="stylesheet" type="text/css" />
<?php };

$hBodyEnd = function () {
	global $mixes; ?>
	<script src="js/common.js"></script>
	<script src="js/order.js"></script>
	<script>
	let mixes = JSON.parse(atob("<?= base64_encode(json_encode($mixes)) ?>"));
	htmlSetup(mixes);
	</script>
<?php };

show_html_start_block(PageIndex::Order); ?>
	<h1 class="text-center">Order Mixes</h1>
<?php show_messages(); ?>
	<form action="#" method="post" class="order">
		<div class="order-mix-selector">
			<table class="table">
				<tr>
					<th>Name</th>
					<th>Price</th>
					<th>Is XXL?</th>
					<th>Quantity</th>
					<th>Total</th>
				</tr>
				<?php foreach ($mixes as $id => $mix) { ?>
					<tr class="order-mix-row" data-mix-id="<?= $id ?>">
						<td><?= h($mix->name) ?></td>
						<td class="number-align"><?= h($mix->cost) ?></td>
						<td class="number-align">
							<input
								class="order-mix-input-isxxl"
								type="checkbox"
								name="orders[<?= $id ?>][isXXL]"
								value="1" />
						</td>
						<td class="number-align">
							<input
								class="order-mix-input-quantity"
								type="number"
								name="orders[<?= $id ?>][quantity]"
								min="0"
								step="1"
								value="0" />
						</td>
						<td class="number-align order-mix-output-total-cost">0.00</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<div class="order-costs">
			<div class="order-cost">
				<div class="cost-title s5">Initial</div>
				<input
					id="cost-init"
					name="costInit"
					type="text"
					readonly
					class="cost-value number-align s5"
					value="00.00" />
			</div>
			<div class="order-cost">
				<div class="cost-title s5">Shipping</div>
				<input
					id="cost-shipping"
					name="costShipping"
					type="text"
					readonly
					class="cost-value number-align s5"
					value="00.00" />
			</div>
			<div class="order-cost">
				<div class="cost-title s5">Taxes</div>
				<input
					id="cost-taxes"
					name="costTaxes"
					type="text"
					readonly
					class="cost-value number-align s5"
					value="00.00" />
			</div>
			<div class="order-cost">
				<div class="cost-title s4">Total</div>
				<input
					id="cost-total"
					name="costTotal"
					type="text"
					readonly
					class="cost-value number-align s3"
					value="00.00" />
			</div>
			<button class="s4" type="submit" name="submit" value="1">Place Order</button>
		</div>
	</form>
<?php show_html_end_block();

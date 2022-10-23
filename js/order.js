function htmlSetup(mixes) {
	let orders       = mixes;
	let costMuesli   = 0.0;
	let costShipping = 0.0;
	let costTaxes    = 0.0;
	let costTotal    = 0.0;

	let $$costInit     = document.getElementById("cost-init");
	let $$costShipping = document.getElementById("cost-shipping");
	let $$costTaxes    = document.getElementById("cost-taxes");
	let $$costTotal    = document.getElementById("cost-total");
	let order$$rows    = document.getElementsByClassName("order-mix-row");

	function updateOrderCostsAnd$$() {
		costMuesli = 0;
		for (let orderId in orders) {
			if (!orders.hasOwnProperty(orderId)) continue;
			let order = orders[orderId];
			if (order.quantity === 0) continue;
			costMuesli += order.totalCost;
		}
		costShipping         = costMuesli >= 2000 ? 0 : (/*in india?*/ true ? 400 : 600);
		costTaxes            = costMuesli * 0.105;
		costTotal            = costMuesli + costShipping + costTaxes;
		$$costInit.value     = roundStr(costMuesli);
		$$costShipping.value = roundStr(costShipping);
		$$costTaxes.value    = roundStr(costTaxes);
		$$costTotal.value    = roundStr(costTotal);
	}

	function orderUpdateAnd$$() {
		if (this.quantity === 0) {
			this.totalCost = 0;
		} else {
			this.totalCost = (this.isXXL ? 4 : 1) * this.quantity * this.cost;
		}
		this.$$row.$totalCost.textContent = roundStr(this.totalCost);
		updateOrderCostsAnd$$();
	}

	function addOrderProps(order) {
		order.isXXL     = false;
		order.quantity  = 0;
		order.totalCost = 0;
		order.update$$  = orderUpdateAnd$$;
	}

	for (let orderId in orders) {
		if (!orders.hasOwnProperty(orderId)) continue;
		addOrderProps(orders[orderId]);
	}

	for (let order$$row of order$$rows) {
		let id = parseInt(order$$row.getAttribute("data-mix-id"));
		if (isNaN(id)) continue;
		let order             = orders[id];
		let $isXXL            = order$$row.getElementsByClassName("order-mix-input-isxxl")[0];
		let $quantity         = order$$row.getElementsByClassName("order-mix-input-quantity")[0];
		let $totalCost        = order$$row.getElementsByClassName("order-mix-output-total-cost")[0];
		order.$$row           = order$$row;
		order$$row.order      = order;
		order$$row.$isXXL     = $isXXL;
		order$$row.$quantity  = $quantity;
		order$$row.$totalCost = $totalCost;
		$quantity.onkeypress  = function (ev) {
			if (ev.keyCode === 13) ev.preventDefault();
		};
		$isXXL.onchange       = function () {
			order.isXXL = !!$isXXL.checked;
			order.update$$();
		};
		$quantity.onchange    = function () {
			order.quantity = parseInt($quantity.value);
			order.update$$();
		};
	}
}

function $updateGramsView(grams) {
	this.$gramsView.textContent  = roundStr(grams) + " g";
	this.$gramsHiddenInput.value = round(grams);
}

// returns HTMLLIElement elements with a $updateGramsView additional method
function add$$mixElem(ing, grams, isFiller, $$to) {
	let cssClass          = "button";
	let fillerHiddenInput = "";
	if (isFiller) {
		fillerHiddenInput = `<input type="hidden" name="ingredients[${+ing.id}][isFiller]" value="1">`;
		cssClass          = "button disabled filler-ingredient";
	}
	let elem                 = `
<li class="${cssClass}">
	<input class="grams-hidden-input" type="hidden" name="ingredients[${+ing.id}][grams]" value="${grams}">
	${fillerHiddenInput}
	<div class="grams-view">${roundStr(grams)} g</div>
	<div class="name-view">${ing.htmlSafeName}</div>
</li>`;
	let $$elem               = htmlToElement(elem);
	$$elem.$gramsView        = $$elem.getElementsByClassName("grams-view")[0];
	$$elem.$gramsHiddenInput = $$elem.getElementsByClassName("grams-hidden-input")[0];
	$$elem.$updateGramsView  = $updateGramsView;
	$$to.appendChild($$elem);
	return $$elem;
}

function htmlSetup(ings) {
	let hasFiller           = false;
	let fillerIngredient    = null;
	let fillerMixIngredient = null;
	let mixIngredients      = [];
	let mixCost             = 0.0;
	let mixKcal             = 0.0;

	let ingsById = {};
	for (let i = 0; i < ings.length; ++i) {
		let ing          = ings[i];
		ingsById[ing.id] = ing;
	}

	let $$mixCostSpan                = document.getElementById("mix-cost-span");
	let $$mixKcalSpan                = document.getElementById("mix-kcal-span");
	let $$mixIngredients             = document.getElementById("mix-ingredients");
	let $$fillerIngredient           = null;
	let $$mixIngredients$ingredients = [];
	let $$forms                      = document.getElementsByClassName("mixer-selector-ingredient");

	function updateFillerMixIngredientAnd$$() {
		mixCost        = 0.0;
		mixKcal        = 0.0;
		let totalGrams = 0.0;
		for (let i = 0; i < mixIngredients.length; ++i) {
			let mixIng = mixIngredients[i];
			totalGrams += mixIng.grams;
			mixCost += mixIng.cost;
			mixKcal += mixIng.kcal;
		}
		let fillerGrams     = round(MAX_GRAMS - totalGrams);
		fillerMixIngredient = getMixIngredient(fillerIngredient, fillerGrams);
		mixCost += fillerMixIngredient.cost;
		mixKcal += fillerMixIngredient.kcal;
		$$fillerIngredient.$updateGramsView(fillerGrams);
		$$mixCostSpan.textContent = round(mixCost);
		$$mixKcalSpan.textContent = round(mixKcal);
	}

	function onAddIngredientFormSubmit(ev) {
		ev.preventDefault();
		let id  = +ev.target.getElementsByClassName("m-s-i-hidden-id")[0].value;
		let ing = ingsById[id];
		if (!hasFiller) {
			let grams           = MAX_GRAMS;
			let $$elem          = add$$mixElem(ing, grams, true, $$mixIngredients);
			hasFiller           = true;
			fillerIngredient    = ing;
			fillerMixIngredient = getMixIngredient(fillerIngredient, grams);
			$$fillerIngredient  = $$elem;
		} else {
			let gramsStr = prompt("Enter the number of grams of the ingredient to add to the muesli:", "10");
			if (gramsStr == null || gramsStr === "") return;
			let grams = round(parseFloat(gramsStr));
			if (grams === 0) return;
			if (isNaN(grams) || grams < 0) {
				alert("Enter a valid number.");
				return;
			}

			if (mixIngredients.length >= 12) {
				alert(`A muesli mix can only contain ${MAX_INGREDIENTS} ingredients + 1 filler ingredient.`);
				return;
			}
			if (fillerMixIngredient.grams - grams <= 0) {
				alert(`Total ingredients mass must not exceed ${MAX_GRAMS} grams.`);
				return;
			}
			if (fillerIngredient.id === ing.id) {
				alert(`This ingredient has already been used as the filler.`);
				return;
			}
			let ingAlreadyAdded = false;
			for (let i = 0; i < mixIngredients.length; ++i) {
				if (mixIngredients[i].ingredient.id === ing.id) {
					ingAlreadyAdded = true;
					break;
				}
			}
			if (ingAlreadyAdded) {
				alert(`This ingredient has already been added once.`);
				return;
			}

			let $$elem        = add$$mixElem(ing, grams, false, $$mixIngredients);
			let mixIngredient = getMixIngredient(ing, grams);
			mixIngredients.push(mixIngredient);
			$$mixIngredients$ingredients.push($$elem);
		}
		updateFillerMixIngredientAnd$$();
	}

	for (let $$f of $$forms) {
		$$f.onsubmit = onAddIngredientFormSubmit;
	}
}

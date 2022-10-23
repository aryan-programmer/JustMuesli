// $$ represents DOM elements

const MAX_GRAMS       = 600.00;
const MAX_INGREDIENTS = 12;

function roundStr(v) {
	return v.toFixed(2);
}

function round(v) {
	return +v.toFixed(2);
}

function htmlToElement(html) {
	let template       = document.createElement("template");
	html               = html.trim();
	template.innerHTML = html;
	return template.content.firstChild;
}

function getMixIngredient(ingredient, grams) {
	return {
		ingredient  : ingredient,
		grams       : grams,
		cost        : ingredient.costPer600g / MAX_GRAMS * grams,
		kcalCarbs   : ingredient.kcalCarbsPer100g / 100 * grams,
		kcalProteins: ingredient.kcalProteinsPer100g / 100 * grams,
		kcalFats    : ingredient.kcalFatsPer100g / 100 * grams,
		kcal        : ingredient.kcalPer100g / 100 * grams,
	};
}

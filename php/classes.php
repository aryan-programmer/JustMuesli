<?php require_once "consts.php";

class DbInsertMixIngredient {
	public $ingredient;
	public $grams;
	public $isFiller;

	public function __construct($ingredient,
	                            $grams,
	                            $isFiller) {
		$this->ingredient = $ingredient;
		$this->grams      = rnd($grams);
		$this->isFiller   = !($isFiller == 0);
	}
}

class DbMixOrderRow {
	public $mixId;
	public $quantity;
	public $isXXL;

	public function __construct($mixId,
	                            $quantity,
	                            $isXXL) {
		$this->mixId    = (int)$mixId;
		$this->quantity = (int)$quantity;
		$this->isXXL    = $isXXL === true;
	}
}

class DbInsertOrder {
	public $mixOrders;
	public $costInit;
	public $costShipping;
	public $costTaxes;

	public function __construct($mixOrders,
	                            $costInit,
	                            $costShipping,
	                            $costTaxes) {
		$this->mixOrders    = $mixOrders;
		$this->costInit     = rnd($costInit);
		$this->costShipping = rnd($costShipping);
		$this->costTaxes    = rnd($costTaxes);
	}
}

class Ingredient {
	public $id;
	public $name;
	public $categoryId;
	public $categoryName;
	public $costPer600g;
	public $kcalCarbsPer100g;
	public $kcalProteinsPer100g;
	public $kcalFatsPer100g;
	public $kcalPer100g;

	public $htmlSafeName;
	public $htmlSafeCategoryName;

	public function __construct($id,
	                            $name,
	                            $categoryId,
	                            $categoryName,
	                            $costPer600g,
	                            $kcalCarbsPer100g,
	                            $kcalProteinsPer100g,
	                            $kcalFatsPer100g,
	                            $kcalPer100g) {
		$this->id                  = (int)$id;
		$this->name                = $name;
		$this->categoryId          = (int)$categoryId;
		$this->categoryName        = $categoryName;
		$this->costPer600g         = rnd($costPer600g);
		$this->kcalCarbsPer100g    = rnd($kcalCarbsPer100g);
		$this->kcalProteinsPer100g = rnd($kcalProteinsPer100g);
		$this->kcalFatsPer100g     = rnd($kcalFatsPer100g);
		$this->kcalPer100g         = rnd($kcalPer100g);

		$this->htmlSafeName         = h($name);
		$this->htmlSafeCategoryName = h($categoryName);
	}
}

class MixIngredient extends DbInsertMixIngredient {
	public $cost;
	public $kcal;
	public $kcalCarbs;
	public $kcalProteins;
	public $kcalFats;

	public function __construct($ingredient,
	                            $grams,
	                            $isFiller) {
		parent::__construct($ingredient, $grams, $isFiller);
		$this->cost         = rnd($ingredient->costPer600g / MAX_GRAMS * $this->grams);
		$this->kcal         = rnd($ingredient->kcalPer100g / 100 * $this->grams);
		$this->kcalCarbs    = rnd($ingredient->kcalCarbsPer100g / 100 * $this->grams);
		$this->kcalProteins = rnd($ingredient->kcalProteinsPer100g / 100 * $this->grams);
		$this->kcalFats     = rnd($ingredient->kcalFatsPer100g / 100 * $this->grams);
	}
}

class Mix {
	public $id;
	public $name;
	public $mixIngredients;
	public $cost;
	public $kcal;
	public $kcalCarbs;
	public $kcalProteins;
	public $kcalFats;

	public function __construct($id, $name, $mixIngredients) {
		$this->id             = $id;
		$this->name           = $name;
		$this->mixIngredients = $mixIngredients;
		$cost                 = 0.0;
		$kcal                 = 0.0;
		$kcalCarbs            = 0.0;
		$kcalProteins         = 0.0;
		$kcalFats             = 0.0;
		foreach ($mixIngredients as $mixIng) {
			$cost         += $mixIng->cost;
			$kcal         += $mixIng->kcal;
			$kcalCarbs    += $mixIng->kcalCarbs;
			$kcalProteins += $mixIng->kcalProteins;
			$kcalFats     += $mixIng->kcalFats;
		}
		$this->cost         = rnd($cost);
		$this->kcal         = rnd($kcal);
		$this->kcalCarbs    = rnd($kcalCarbs);
		$this->kcalProteins = rnd($kcalProteins);
		$this->kcalFats     = rnd($kcalFats);
	}
}

class UserDetails {
	public $address;
	public $zip;
	public $city;
	public $country;
	public $phone;
	public $name;

	public function __construct($address,
	                            $zip,
	                            $city,
	                            $country,
	                            $phone,
	                            $name) {
		$this->address = $address ?? "";
		$this->zip     = $zip ?? "";
		$this->city    = $city ?? "";
		$this->country = $country ?? "";
		$this->phone   = $phone ?? "";
		$this->name    = $name ?? "";
	}

}

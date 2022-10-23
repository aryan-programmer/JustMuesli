USE just_mueslil;

-- signIn
SELECT id, email, password
FROM users
WHERE email = ?;

-- signUp
INSERT INTO users (email, password)
	VALUE (?, ?);

-- getIngredients
SELECT i.id,
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
ORDER BY category_id, i.name;

-- getMixIngredientsOfMixById
SELECT i.id    AS ing_id,
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
ORDER BY mil.grams DESC;

-- getMixById
SELECT id, name, user_id
FROM mixes
WHERE id = ?;

-- getMixNamesAssocById
SELECT id, name, user_id
FROM mixes
WHERE user_id = ?
ORDER BY name;

-- getUserDetails
SELECT address,
       zip,
       city,
       country,
       phone,
       name
FROM users
WHERE id = ?;

-- addMix
INSERT INTO mixes (name, user_id)
	VALUE (?, ?);
INSERT INTO mix_ingredients_list (is_filler, mix_id, ingredient_id, grams)
	VALUE (?, ?, ?, ?);

-- placeOrder
INSERT INTO orders (user_id, mixes_num, cost_init, cost_shipping, cost_taxes)
	VALUE (?, ?, ?, ?, ?);
INSERT INTO order_mixes_list (mix_id, order_id, quantity, isXXL)
	VALUE (?, ?, ?, ?);

-- deleteMixById
DELETE
FROM mixes
WHERE id = ?
  AND user_id = ?;
  
-- setUserDetails
UPDATE users
SET address = ?,
    zip     = ?,
    city    = ?,
    country = ?,
    phone   = ?,
    name    = ?
WHERE id = ?;

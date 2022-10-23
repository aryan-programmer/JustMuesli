USE just_muesli;

-- Debug only
INSERT INTO users (email, password) VALUE ('a@a.com', '$2y$10$aTWnNfegFdTUXyGIscS8ruiJBswgVXzj0QR.FTH9sdugf998FrEpK');

INSERT INTO ingredient_categories (name)
VALUES ('Basic'),
       ('Category1'),
       ('Category2');

DROP PROCEDURE IF EXISTS fill_ingredients;
DROP PROCEDURE IF EXISTS fill_mixes;

DELIMITER $$

CREATE PROCEDURE fill_ingredients()
BEGIN
	DECLARE v1 INT DEFAULT 25;
	DECLARE carbs DECIMAL(5, 2);
	DECLARE fats DECIMAL(5, 2);
	DECLARE proteins DECIMAL(5, 2);
	DECLARE total DECIMAL(5, 2);
	DELETE
	FROM ingredients
	WHERE TRUE;

	WHILE v1 > 0
		DO
			SET carbs = RAND() * 100;
			SET fats = RAND() * 75;
			SET proteins = RAND() * 30;
			SET total = carbs + fats + proteins;
			INSERT INTO ingredients(name, category_id, cost_per_600g, kcal_carbs_per_100g, kcal_proteins_per_100g,
			                        kcal_fats_per_100g, kcal_per_100g)
			VALUES (CONCAT('Muesli ingredient #', v1),
			        FLOOR((RAND()) * (3)) + 1,
			        RAND() * 250,
			        carbs,
			        proteins,
			        fats,
			        total);
			SET v1 = v1 - 1;
		END WHILE;
END $$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE fill_mixes()
BEGIN
	DECLARE v1 INT DEFAULT 10;
	DECLARE v2 INT;
	DECLARE mid INT;
	DECLARE ings INT;
	DECLARE maxGrams DECIMAL(5, 2);
	DECLARE grams DECIMAL(5, 2);
	WHILE v1 > 0
		DO
			INSERT INTO mixes(name, user_id)
			VALUES (CONCAT('Muesli mix #', v1), 1);
			SET mid = LAST_INSERT_ID();
			SET ings = FLOOR((RAND()) * (12)) + 1;
			SET v2 = ings;
			SET maxGrams = 0;

			WHILE v2 > 0
				DO
					SET grams = RAND() * 40;
					INSERT INTO mix_ingredients_list(is_filler, mix_id, ingredient_id, grams)
					VALUES (0, mid, FLOOR((RAND()) * (12)) + 1, grams);
					SET maxGrams = maxGrams + grams;
					SET v2 = v2 - 1;
				END WHILE;
			INSERT INTO mix_ingredients_list(is_filler, mix_id, ingredient_id, grams)
			VALUES (1, mid, FLOOR((RAND()) * (12)) + 1, 600 - maxGrams);
			SET v1 = v1 - 1;
		END WHILE;
END $$

DELIMITER ;

CALL fill_ingredients();
CALL fill_mixes();

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

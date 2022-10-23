USE just_muesli;

DROP TABLE IF EXISTS order_mixes_list;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS mix_ingredients_list;
DROP TABLE IF EXISTS mixes;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS ingredient_categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
	id       INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email    VARCHAR(256)  NOT NULL UNIQUE,
	password VARCHAR(1024) NOT NULL,
	address  VARCHAR(1024) NULL,
	zip      VARCHAR(16)   NULL,
	city     VARCHAR(256)  NULL,
	country  VARCHAR(256)  NULL,
	phone    VARCHAR(32)   NULL,
	name     VARCHAR(256)  NULL
);

CREATE TABLE ingredient_categories (
	id   INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(256) NOT NULL UNIQUE
);

CREATE TABLE ingredients (
	id                     INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name                   VARCHAR(256) NOT NULL UNIQUE,
	category_id            INT          NOT NULL,
	cost_per_600g          DECIMAL(7, 2),
	kcal_carbs_per_100g    DECIMAL(7, 2),
	kcal_proteins_per_100g DECIMAL(7, 2),
	kcal_fats_per_100g     DECIMAL(7, 2),
	kcal_per_100g          DECIMAL(7, 2),
	FOREIGN KEY (category_id)
		REFERENCES ingredient_categories (id)
		ON DELETE CASCADE
);

CREATE TABLE mixes (
	id      INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name    VARCHAR(256) NOT NULL UNIQUE,
	user_id INT          NOT NULL,
	FOREIGN KEY (user_id)
		REFERENCES users (id)
		ON DELETE CASCADE
);

CREATE TABLE mix_ingredients_list (
	id            INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
	is_filler     BOOL          NOT NULL DEFAULT FALSE,
	mix_id        INT           NOT NULL,
	ingredient_id INT           NOT NULL,
	grams         DECIMAL(7, 2) NOT NULL,
	FOREIGN KEY (mix_id)
		REFERENCES mixes (id)
		ON DELETE CASCADE,
	FOREIGN KEY (ingredient_id)
		REFERENCES ingredients (id)
		ON DELETE CASCADE
);

CREATE TABLE orders (
	id             INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_id        INT           NOT NULL,
	mixes_num      INT           NOT NULL,
	placement_time TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	cost_init      DECIMAL(7, 2) NOT NULL,
	cost_shipping  DECIMAL(7, 2) NOT NULL,
	cost_taxes     DECIMAL(7, 2) NOT NULL,
	status         INT           NOT NULL DEFAULT 0,
	FOREIGN KEY (user_id)
		REFERENCES users (id)
		ON DELETE CASCADE
);

CREATE TABLE order_mixes_list (
	id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	mix_id   INT NOT NULL,
	order_id INT NOT NULL,
	quantity INT NOT NULL,
	isXXL    BIT NOT NULL DEFAULT 0,
	FOREIGN KEY (mix_id)
		REFERENCES mixes (id)
		ON DELETE CASCADE,
	FOREIGN KEY (order_id)
		REFERENCES orders (id)
		ON DELETE CASCADE
);

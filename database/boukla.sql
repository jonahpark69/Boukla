CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ref VARCHAR(32) NOT NULL,
  created_at DATETIME NOT NULL,
  first VARCHAR(64),
  last VARCHAR(64),
  email VARCHAR(128),
  phone VARCHAR(32),
  addr1 VARCHAR(128),
  addr2 VARCHAR(128),
  zip VARCHAR(16),
  city VARCHAR(64),
  country VARCHAR(32),
  billing_addr1 VARCHAR(128),
  billing_zip VARCHAR(16),
  billing_city VARCHAR(64),
  billing_country VARCHAR(32),
  same_billing TINYINT(1),
  pay_method VARCHAR(16),
  subtotal INT,
  shipping INT,
  total INT
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_name VARCHAR(128),
  qty INT,
  price INT,
  FOREIGN KEY (order_id) REFERENCES orders(id)
);
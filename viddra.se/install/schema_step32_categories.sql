-- Step 32: Categories per household

CREATE TABLE IF NOT EXISTS viddra_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  household_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  type ENUM('fixed','variable','saving') NOT NULL DEFAULT 'variable',
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cat_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

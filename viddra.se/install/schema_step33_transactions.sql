-- Step 33: Transactions (manual entry first)

CREATE TABLE IF NOT EXISTS viddra_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  household_id INT NOT NULL,
  category_id INT NULL,
  tx_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  note VARCHAR(255) NULL,
  created_by_user_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tx_household_date (household_id, tx_date),
  CONSTRAINT fk_tx_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_tx_category FOREIGN KEY (category_id) REFERENCES viddra_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

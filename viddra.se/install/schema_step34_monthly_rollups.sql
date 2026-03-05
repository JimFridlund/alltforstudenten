-- Step 34: Monthly rollups (optional table for caching)
CREATE TABLE IF NOT EXISTS viddra_monthly_rollups (
  household_id INT NOT NULL,
  ym CHAR(7) NOT NULL, -- YYYY-MM
  category_id INT NULL,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  tx_count INT NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (household_id, ym, category_id),
  CONSTRAINT fk_rollup_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_rollup_category FOREIGN KEY (category_id) REFERENCES viddra_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

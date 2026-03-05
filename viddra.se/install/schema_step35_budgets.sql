-- Step 35: Budgets per category per month (Budget vs Actual)

CREATE TABLE IF NOT EXISTS viddra_budgets (
  household_id INT NOT NULL,
  ym CHAR(7) NOT NULL, -- YYYY-MM
  category_id INT NOT NULL,
  budget_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (household_id, ym, category_id),
  CONSTRAINT fk_budget_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_budget_category FOREIGN KEY (category_id) REFERENCES viddra_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

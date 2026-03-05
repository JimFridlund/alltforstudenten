-- Step 31: Persist onboarding inputs to DB (per household)
CREATE TABLE IF NOT EXISTS viddra_household_financials (
  household_id INT PRIMARY KEY,
  income_a DECIMAL(10,2) NOT NULL DEFAULT 0,
  income_b DECIMAL(10,2) NOT NULL DEFAULT 0,
  fixed_rent DECIMAL(10,2) NOT NULL DEFAULT 0,
  fixed_utilities DECIMAL(10,2) NOT NULL DEFAULT 0,
  fixed_insurance DECIMAL(10,2) NOT NULL DEFAULT 0,
  fixed_subscriptions DECIMAL(10,2) NOT NULL DEFAULT 0,
  goal_key VARCHAR(20) NOT NULL DEFAULT 'emergency',
  goal_target DECIMAL(10,2) NOT NULL DEFAULT 0,
  goal_monthly DECIMAL(10,2) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_fin_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

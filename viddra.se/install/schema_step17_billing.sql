-- Step 17: Billing skeleton (no Stripe integration yet)

CREATE TABLE IF NOT EXISTS viddra_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,           -- e.g. 'plus_monthly'
  name VARCHAR(120) NOT NULL,                 -- e.g. 'Viddra Plus'
  price_gbp_pence INT NOT NULL,               -- 1100 for £11.00
  interval_unit VARCHAR(20) NOT NULL,         -- 'month' | 'year'
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS viddra_subscriptions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  household_id INT NOT NULL,
  plan_id INT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'inactive', -- inactive/trialing/active/past_due/canceled
  current_period_start DATETIME NULL,
  current_period_end DATETIME NULL,
  cancel_at_period_end TINYINT(1) NOT NULL DEFAULT 0,
  created_by_user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sub_household (household_id, status),
  CONSTRAINT fk_sub_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_sub_plan FOREIGN KEY (plan_id) REFERENCES viddra_plans(id) ON DELETE RESTRICT,
  CONSTRAINT fk_sub_creator FOREIGN KEY (created_by_user_id) REFERENCES viddra_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed plan (id=1) if missing
INSERT INTO viddra_plans (id, code, name, price_gbp_pence, interval_unit, is_active, created_at)
VALUES (1, 'plus_monthly', 'Viddra Plus', 1100, 'month', 1, NOW())
ON DUPLICATE KEY UPDATE id=id;

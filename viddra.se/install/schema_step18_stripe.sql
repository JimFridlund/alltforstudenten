-- Step 18: Stripe integration tables/columns

CREATE TABLE IF NOT EXISTS viddra_stripe_customers (
  household_id INT NOT NULL PRIMARY KEY,
  stripe_customer_id VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sc_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE viddra_subscriptions
  ADD COLUMN stripe_subscription_id VARCHAR(120) NULL AFTER plan_id,
  ADD COLUMN stripe_checkout_session_id VARCHAR(120) NULL AFTER stripe_subscription_id;

-- Helpful index
CREATE INDEX IF NOT EXISTS idx_sub_stripe_sub ON viddra_subscriptions (stripe_subscription_id);

-- Step 21: Stripe mapping stabilization

ALTER TABLE viddra_subscriptions
  ADD COLUMN stripe_customer_id VARCHAR(120) NULL AFTER stripe_checkout_session_id;

CREATE INDEX idx_sub_stripe_customer ON viddra_subscriptions (stripe_customer_id);

-- Optional unique to avoid duplicates for same Stripe subscription id
-- (Some MySQL versions don't allow IF NOT EXISTS for unique index; run manually if needed)
-- ALTER TABLE viddra_subscriptions ADD UNIQUE KEY uq_sub_stripe_subscription (stripe_subscription_id);

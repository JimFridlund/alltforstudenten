-- Step 23: Email verification

ALTER TABLE viddra_users
  ADD COLUMN email_verified_at DATETIME NULL AFTER email,
  ADD COLUMN email_verify_token_hash CHAR(64) NULL AFTER email_verified_at,
  ADD COLUMN email_verify_sent_at DATETIME NULL AFTER email_verify_token_hash;

CREATE INDEX idx_users_email_verified ON viddra_users (email_verified_at);
CREATE INDEX idx_users_verify_token ON viddra_users (email_verify_token_hash);

-- Step 25: Invite expiry + revoke + resend support

ALTER TABLE viddra_household_invites
  ADD COLUMN expires_at DATETIME NULL AFTER created_at,
  ADD COLUMN revoked_at DATETIME NULL AFTER expires_at,
  ADD COLUMN revoked_by_user_id INT NULL AFTER revoked_at,
  ADD COLUMN resent_at DATETIME NULL AFTER revoked_by_user_id,
  ADD COLUMN resent_count INT NOT NULL DEFAULT 0 AFTER resent_at;

CREATE INDEX idx_inv_household_status ON viddra_household_invites (household_id, status);
CREATE INDEX idx_inv_expires ON viddra_household_invites (expires_at);

-- Step 26: Optional copy-link helper for pending invites
-- Stores last generated token in plaintext for admin/dev use (NOT recommended for production).
-- If you don't want this, skip this SQL and set VIDDRA_SHOW_INVITE_COPY_LINK=false.

ALTER TABLE viddra_household_invites
  ADD COLUMN last_token_plain VARCHAR(80) NULL AFTER token_hash;

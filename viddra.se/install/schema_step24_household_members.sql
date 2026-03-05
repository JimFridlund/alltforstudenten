-- Step 24: Household membership + invite acceptance (verified email)

CREATE TABLE IF NOT EXISTS viddra_household_members (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  household_id INT NOT NULL,
  user_id INT NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'member', -- owner/member
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_household_user (household_id, user_id),
  INDEX idx_member_user (user_id),
  CONSTRAINT fk_hm_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_hm_user FOREIGN KEY (user_id) REFERENCES viddra_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure household owner is a member (best effort; run manually if you want to backfill)
-- INSERT IGNORE INTO viddra_household_members (household_id, user_id, role)
-- SELECT h.id, h.owner_user_id, 'owner' FROM viddra_households h;

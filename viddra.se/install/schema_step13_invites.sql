-- Step 13: Household invites

CREATE TABLE IF NOT EXISTS viddra_household_invites (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  household_id INT NOT NULL,
  email VARCHAR(190) NOT NULL,
  token_hash CHAR(64) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending/accepted/cancelled/expired
  created_by_user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  accepted_at DATETIME NULL,
  INDEX idx_invite_household (household_id),
  INDEX idx_invite_email_status (email, status),
  UNIQUE KEY uq_invite_token_hash (token_hash),
  CONSTRAINT fk_inv_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  CONSTRAINT fk_inv_creator FOREIGN KEY (created_by_user_id) REFERENCES viddra_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

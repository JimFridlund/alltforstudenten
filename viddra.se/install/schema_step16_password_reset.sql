-- Step 16: Password reset tokens

CREATE TABLE IF NOT EXISTS viddra_password_resets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending/used/expired
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  used_at DATETIME NULL,
  INDEX idx_pr_user_status (user_id, status),
  UNIQUE KEY uq_pr_token_hash (token_hash),
  CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES viddra_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 12: Households-first schema changes (shared scenarios)

-- Ensure households tables exist (from Step 10)
CREATE TABLE IF NOT EXISTS viddra_households (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS viddra_user_households (
  user_id INT NOT NULL,
  household_id INT NOT NULL,
  role VARCHAR(30) NOT NULL DEFAULT 'member',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, household_id),
  INDEX idx_vuh_household (household_id),
  CONSTRAINT fk_vuh_user FOREIGN KEY (user_id) REFERENCES viddra_users(id) ON DELETE CASCADE,
  CONSTRAINT fk_vuh_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Scenarios become household-scoped:
ALTER TABLE viddra_scenarios
  ADD COLUMN household_id INT NULL AFTER id,
  ADD COLUMN created_by_user_id INT NULL AFTER household_id;

-- Backfill for existing rows (best effort)
-- If you already have households, you may want to map properly.
UPDATE viddra_scenarios SET household_id = 1 WHERE household_id IS NULL;

-- If old user_id exists, copy it into created_by_user_id
UPDATE viddra_scenarios SET created_by_user_id = user_id WHERE created_by_user_id IS NULL AND user_id IS NOT NULL;

-- Make columns NOT NULL (after backfill)
ALTER TABLE viddra_scenarios
  MODIFY household_id INT NOT NULL,
  MODIFY created_by_user_id INT NOT NULL;

-- Add FKs + indexes
ALTER TABLE viddra_scenarios
  ADD INDEX idx_scen_household_time (household_id, updated_at),
  ADD CONSTRAINT fk_scen_household FOREIGN KEY (household_id) REFERENCES viddra_households(id) ON DELETE CASCADE,
  ADD CONSTRAINT fk_scen_creator FOREIGN KEY (created_by_user_id) REFERENCES viddra_users(id) ON DELETE CASCADE;

-- Seed a default household with id=1 if missing (to support backfill)
INSERT INTO viddra_households (id, name, created_at)
VALUES (1, 'Default household', NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Link user 1 to household 1 if exists
INSERT INTO viddra_user_households (user_id, household_id, role, created_at)
VALUES (1, 1, 'owner', NOW())
ON DUPLICATE KEY UPDATE role=role;

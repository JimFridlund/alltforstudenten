-- Step 20: Stripe webhook event log

CREATE TABLE IF NOT EXISTS viddra_webhook_events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  provider VARCHAR(30) NOT NULL DEFAULT 'stripe',
  event_id VARCHAR(120) NULL,
  event_type VARCHAR(120) NULL,
  status_code INT NOT NULL DEFAULT 0,
  signature_ok TINYINT(1) NOT NULL DEFAULT 0,
  error_message VARCHAR(500) NULL,
  payload_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_provider_event (provider, event_id),
  INDEX idx_provider_type_time (provider, event_type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

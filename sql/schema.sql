-- Minimal IDC System schema
-- MySQL 8+

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  balance DECIMAL(16,4) NOT NULL DEFAULT 0,
  status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  phone VARCHAR(32) NULL,
  oauth_provider VARCHAR(32) NULL,
  oauth_id VARCHAR(128) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  delivery_mode ENUM('manual','provider_api') NOT NULL DEFAULT 'manual',
  billing_mode ENUM('periodic','metered') NOT NULL DEFAULT 'periodic',
  price_json JSON NOT NULL,
  stock INT NULL,
  provider_code VARCHAR(64) NULL,
  provider_plan_id VARCHAR(128) NULL,
  status ENUM('active','hidden','disabled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(16,4) NOT NULL,
  status ENUM('pending','paid','provisioning','active','failed','refunded') NOT NULL DEFAULT 'pending',
  billing_cycle VARCHAR(32) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  paid_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_orders_user_id (user_id),
  KEY idx_orders_status (status),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_orders_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transactions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  change_amount DECIMAL(16,4) NOT NULL,
  type ENUM('topup','consume','refund','adjust') NOT NULL,
  ref_type VARCHAR(32) NULL,
  ref_id BIGINT UNSIGNED NULL,
  `desc` VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_transactions_user_id (user_id),
  CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS services (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NULL,
  status ENUM('pending','active','suspended','terminated') NOT NULL DEFAULT 'pending',
  delivery_mode ENUM('manual','provider_api') NOT NULL DEFAULT 'manual',
  provider_service_id VARCHAR(128) NULL,
  ip VARCHAR(64) NULL,
  port INT NOT NULL DEFAULT 22,
  username VARCHAR(64) NULL,
  password_enc TEXT NULL,
  expire_at DATETIME NULL,
  meter_started_at DATETIME NULL,
  last_billed_at DATETIME NULL,
  unit_price_per_second DECIMAL(16,8) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_services_user_id (user_id),
  KEY idx_services_status (status),
  CONSTRAINT fk_services_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_services_product FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_services_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS monitor_tokens (
  service_id BIGINT UNSIGNED NOT NULL,
  token VARCHAR(128) NOT NULL,
  status ENUM('active','revoked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_seen_at TIMESTAMP NULL,
  PRIMARY KEY (service_id),
  UNIQUE KEY uniq_monitor_tokens_token (token),
  CONSTRAINT fk_monitor_tokens_service FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS monitor_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  service_id BIGINT UNSIGNED NOT NULL,
  cpu DECIMAL(5,2) NOT NULL,
  mem DECIMAL(5,2) NOT NULL,
  disk DECIMAL(5,2) NOT NULL,
  net_up BIGINT UNSIGNED NOT NULL DEFAULT 0,
  net_down BIGINT UNSIGNED NOT NULL DEFAULT 0,
  load1 DECIMAL(6,2) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_monitor_logs_service_id_created_at (service_id, created_at),
  CONSTRAINT fk_monitor_logs_service FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS provider_configs (
  provider_code VARCHAR(64) NOT NULL,
  config_json JSON NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (provider_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

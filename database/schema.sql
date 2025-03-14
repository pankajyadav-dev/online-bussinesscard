CREATE DATABASE IF NOT EXISTS business_card_db;
USE business_card_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    job_title VARCHAR(100),
    address TEXT,
    website VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS card_designs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    template_path VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    design_id INT NOT NULL,
    card_name VARCHAR(100) NOT NULL,
    custom_fields JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (design_id) REFERENCES card_designs(id)
);

INSERT INTO card_designs (name, template_path, category) VALUES
('Classic White', 'templates/classic_white.php', 'Professional'),
('Modern Blue', 'templates/modern_blue.php', 'Professional'),
('Creative Red', 'templates/creative_red.php', 'Creative'),
('Minimalist Black', 'templates/minimalist_black.php', 'Minimalist'),
('Corporate Grey', 'templates/corporate_grey.php', 'Corporate'); 
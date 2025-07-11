CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  verification_code VARCHAR(255),
  is_verified BOOLEAN DEFAULT 0
);

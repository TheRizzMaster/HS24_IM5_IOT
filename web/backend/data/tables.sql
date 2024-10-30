CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,  -- UUID
    card_id VARCHAR(255) UNIQUE NOT NULL
    firstname VARCHAR(255),
    lastname VARCHAR(255),
);

CREATE TABLE work_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id CHAR(36),
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    description VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

DELIMITER //

CREATE TRIGGER before_insert_users
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.id IS NULL THEN
        SET NEW.id = UUID();
    END IF;
END //

DELIMITER ;

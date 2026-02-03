CREATE TABLE `cart` (
    `id` INT(11) NOT NULL AUTO_INCREMENT, 
    `user_id` INT(11) NOT NULL, 
    `item_name` VARCHAR(255) NOT NULL, 
    `item_price` DECIMAL(10, 2) NOT NULL, 
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`), 
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

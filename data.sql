CREATE DATABASE  KITShopping;
USE KITShopping;



CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE
);


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shipping_address TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);


INSERT INTO categories (name, slug) VALUES
('Electronics', 'electronics'),
('Clothes', 'clothes'),
('Sports', 'sports');

-- Step 4: Insert Products with Online Image URLs

-- Electronics
INSERT INTO products (category_id, name, description, price, image_url, stock_quantity) VALUES
(1, 'Smartphone X', 'Latest generation smartphone with amazing camera.', 699.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Smartphone%20X.jpg', 50),
(1, 'Laptop Pro', 'Powerful laptop for professionals.', 1299.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Laptop%20Pro.jpg', 30),
(1, 'Wireless Headphones', 'Noise-cancelling wireless headphones.', 199.50, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Wireless%20Headphones.jpg', 100),
(1, 'Smartwatch Series 5', 'Track your fitness and stay connected.', 249.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Smartwatch%20Series%205.jpg', 75),
(1, '4K Ultra HD TV', '55-inch 4K TV for stunning visuals.', 499.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/4K%20Ultra%20HD%20TV.jpg', 20),
(1, 'Gaming Console NextGen', 'Experience next-gen gaming.', 499.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Gaming%20Console%20NextGen.jpg', 40),
(1, 'Bluetooth Speaker', 'Portable speaker with rich sound.', 79.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Bluetooth%20Speaker.jpg', 120),
(1, 'Digital Camera Alpha', 'Capture stunning photos and videos.', 899.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Digital%20Camera%20Alpha.jpg', 25),
(1, 'E-Reader Scribe', 'Read your favorite books anywhere.', 129.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/E-Reader%20Scribe.jpg', 60),
(1, 'Tablet Air', 'Lightweight tablet for work and play.', 329.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Tablet%20Air.jpg', 50);

-- Clothes
INSERT INTO products (category_id, name, description, price, image_url, stock_quantity) VALUES
(2, 'Cotton T-Shirt', 'Comfortable 100% cotton t-shirt.', 19.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/cottonTshirt.jpg', 200),
(2, 'Denim Jeans', 'Classic fit denim jeans.', 49.50, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/denimeJeans.jpg', 150),
(2, 'Hooded Sweatshirt', 'Warm and cozy hooded sweatshirt.', 39.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Hooded%20Sweatshirt.jpg', 100),

(2, 'Leather Jacket', 'Stylish genuine leather jacket.', 199.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Leather%20Jacket.jpg', 30),

(2, 'Formal Shirt', 'Crisp white formal shirt.', 45.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Formal%20Shirt.jpg', 90),

(2, 'Polo Shirt', 'Classic polo shirt for casual wear.', 29.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Polo%20Shirt.jpg', 110),
(2, 'Cargo Pants', 'Durable cargo pants with multiple pockets.', 55.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/cargoPants.jpg', 70);

-- Sports
INSERT INTO products (category_id, name, description, price, image_url, stock_quantity) VALUES
(3, 'Basketball Official Size', 'Regulation size basketball.', 29.99, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Basketball%20Official%20Size.jpg', 100),



(3, 'Dumbbell Set 20kg', 'Adjustable dumbbell set.', 79.00, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Dumbbell%20Set%2020kg.jpg', 50),

(3, 'Badminton Set', 'Complete badminton set for two players.', 45.50, 'https://github.com/RohanYadav96k/KIT--Shopping/blob/main/Img/Badminton%20Set.jpg', 70),



-- Step 5: Dummy Users (password = password123)
INSERT INTO users (username, email, password_hash) VALUES
('testuser', 'test@example.com', '$2y$10$N9.O6Ual2JzI3X22gC38IuNzZk.Z27sLwgkZVepgC6N.d0B4u5g3W'),
('rohan', 'rohan@example.com', '$2y$10$gX.gH/d.Z6V0GZ07G.T/u.5oY7tN.J9H.Q1/p0J.T7L.O2m.R6u8e');

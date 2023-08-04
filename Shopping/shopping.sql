CREATE DATABASE shopping;

USE shopping;

-- Create the 'products' table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
);

INSERT INTO products (name, description, price, image)
VALUES ('book', ' A bound collection of pages used to convey information or tell a story.', 9.99, 'book.jpg'),
       ('gold bar', ' Credit Suisse 1 gram Gold Bars from JM Bullion', 77.49, 'gold.jpg'),
       ('mouse', 'A device for controlling the cursor on a computer screen.', 29.99, 'mouse.jpg'),
       ('T-shirt', 'A casual garment made of soft, comfortable fabric.', 8.50, 'T-Shirt.jpg'),
       ('macbook', 'A high-performance laptop computer designed by Apple Inc.', 749.99, 'macbook.jpg'),
       ('notebook', 'A portable book used for writing, drawing, or taking notes.', 14.99, 'notebook.jpg'),
       ('pen', 'A beautiful pen to write beautiful poems.', 49.99, 'pen.jpg'),
       ('camera', 'Capture the moments.', 549.00 , 'camera.jpg');
       


-- Create the 'users' table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    hashed_password VARCHAR(255) NOT NULL
);
-- $user_hash = password_hash('user', PASSWORD_DEFAULT);
-- $admin_hash = password_hash('admin', PASSWORD_DEFAULT);
INSERT INTO users (username, name, email, hashed_password)
VALUES ('user', 'George Bush', 'user@example.com', '$2y$10$X.IOsI42K36EqAq6cVfM7.uh0NERhVUvKJcweUr7KHbCG2nOcswrO'),
       ('admin', 'Barack Obama', 'admin@example.com', '$2y$10$jmRuhiUDnREs.RdYqiTBnenbRs1JQmK46xp/m1Qxelo2wLwhXLuDG');




-- Create the 'orders' table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT ,   --  user_id is NULL when a guest buying without logging in
    FOREIGN KEY (user_id) REFERENCES users(id),
    date DATETIME NOT NULL,
    total DECIMAL(10,2) NOT NULL
);
INSERT INTO orders (user_id, date, total)
VALUES (1, '2023-07-25 13:50:10', 48.97),
       (2, '2023-07-26 14:20:30', 749.99);



-- Create the 'order_items' table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);
INSERT INTO order_items (order_id, product_id, quantity, price)
VALUES (1, 1, 1, 9.99),
       (1, 4, 1, 8.50),
       (1, 6, 2, 14.99),
       (2, 5, 1, 749.99);



-- Create the 'cart' table to store the current items in user shopping cart
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create Users Table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(10) DEFAULT 'buyer'
);

Select * from users;

-- Create Pets Table
CREATE TABLE pets (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50),
    type VARCHAR(20),
    breed VARCHAR(100),
    age INT,
    gender VARCHAR(10),
    city VARCHAR(50),
    description TEXT,
    image VARCHAR(255),
    listed_by INT REFERENCES users(id)
);

Select * from pets;

-- Create Adoption Requests Table
CREATE TABLE adoption_requests (
    id SERIAL PRIMARY KEY,
    pet_id INT REFERENCES pets(id),
    user_id INT REFERENCES users(id),
    status VARCHAR(20) DEFAULT 'pending'
);

Select * from adoption_requests;

-- Insert Users
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@mail.com', '123456', 'admin'),
('Seller User', 'seller@mail.com', '123456', 'seller'),
('Buyer User', 'buyer@mail.com', '123456', 'buyer');

-- Insert Pets
INSERT INTO pets (name, type, breed, age, gender, city, description, image, listed_by) VALUES
('Bruno', 'dog', 'Labrador', 2, 'male', 'Mumbai', 'Friendly dog', 'dog.jpg', 2),
('Mochi', 'cat', 'Persian', 1, 'female', 'Pune', 'Cute cat', 'cat.jpg', 2);

-- Insert Adoption Request
INSERT INTO adoption_requests (pet_id, user_id, status) VALUES
(1, 3, 'pending');

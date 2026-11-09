<?php
// ============================================================
// PRACTICE DATASET: copy this file and run: php dataset.php
// ============================================================

// 1. USERS: associative rows inside an indexed array
// Notes: Tunde has phone = null, Chidi/Zainab/Femi have NO phone key,
// 'skills' is a nested array, some users are inactive.
$users = [
    ['id' => 1,  'name' => 'Ada Obi',      'email' => 'ada@example.com',    'role' => 'admin',  'age' => 29, 'active' => true,  'city' => 'Abuja', 'phone' => '08011111111', 'skills' => ['php', 'vue']],
    ['id' => 2,  'name' => 'Tunde Bello',  'email' => 'tunde@example.com',  'role' => 'user',   'age' => 34, 'active' => true,  'city' => 'Lagos', 'phone' => null,          'skills' => ['python']],
    ['id' => 3,  'name' => 'Chidi Eze',    'email' => 'chidi@example.com',  'role' => 'editor', 'age' => 25, 'active' => false, 'city' => 'Enugu',                           'skills' => ['php', 'laravel', 'mysql']],
    ['id' => 4,  'name' => 'Zainab Musa',  'email' => 'zainab@example.com', 'role' => 'user',   'age' => 41, 'active' => true,  'city' => 'Kano',                            'skills' => []],
    ['id' => 5,  'name' => 'Femi Adeyemi', 'email' => 'femi@example.com',   'role' => 'admin',  'age' => 31, 'active' => false, 'city' => 'Lagos',                           'skills' => ['react-native', 'php']],
    ['id' => 6,  'name' => 'Ngozi Okafor', 'email' => 'ngozi@example.com',  'role' => 'user',   'age' => 22, 'active' => true,  'city' => 'Abuja', 'phone' => '08066666666', 'skills' => ['vue', 'python']],
    ['id' => 7,  'name' => 'Ibrahim Sani', 'email' => 'ibrahim@example.com','role' => 'editor', 'age' => 38, 'active' => true,  'city' => 'Kano',  'phone' => '08077777777', 'skills' => ['php']],
    ['id' => 8,  'name' => 'Grace Udo',    'email' => 'grace@example.com',  'role' => 'user',   'age' => 27, 'active' => true,  'city' => 'Lagos', 'phone' => '08088888888', 'skills' => ['laravel', 'vue']],
    ['id' => 9,  'name' => 'Samuel Ade',   'email' => 'samuel@example.com', 'role' => 'user',   'age' => 45, 'active' => false, 'city' => 'Ibadan','phone' => null,          'skills' => ['python', 'mysql']],
    ['id' => 10, 'name' => 'Halima Yusuf', 'email' => 'halima@example.com', 'role' => 'admin',  'age' => 33, 'active' => true,  'city' => 'Abuja', 'phone' => '08010101010', 'skills' => ['php', 'react-native', 'vue']],
];

// 2. ORDERS: user_id links to $users['id']
// Statuses: paid, pending, cancelled. Some users have several orders, some have none.
$orders = [
    ['id' => 101, 'user_id' => 1,  'status' => 'paid',      'total' => 15000, 'created_at' => '2026-10-01'],
    ['id' => 102, 'user_id' => 2,  'status' => 'pending',   'total' => 4500,  'created_at' => '2026-10-02'],
    ['id' => 103, 'user_id' => 1,  'status' => 'paid',      'total' => 22000, 'created_at' => '2026-10-03'],
    ['id' => 104, 'user_id' => 6,  'status' => 'cancelled', 'total' => 8000,  'created_at' => '2026-10-04'],
    ['id' => 105, 'user_id' => 8,  'status' => 'paid',      'total' => 12500, 'created_at' => '2026-10-05'],
    ['id' => 106, 'user_id' => 2,  'status' => 'paid',      'total' => 3000,  'created_at' => '2026-10-06'],
    ['id' => 107, 'user_id' => 10, 'status' => 'pending',   'total' => 30000, 'created_at' => '2026-10-07'],
    ['id' => 108, 'user_id' => 7,  'status' => 'paid',      'total' => 9500,  'created_at' => '2026-10-08'],
    ['id' => 109, 'user_id' => 8,  'status' => 'cancelled', 'total' => 6000,  'created_at' => '2026-10-09'],
    ['id' => 110, 'user_id' => 1,  'status' => 'pending',   'total' => 18000, 'created_at' => '2026-10-10'],
];

// 3. PRODUCTS: one is out of stock (stock = 0)
$products = [
    ['id' => 1, 'name' => 'Laptop',     'category' => 'electronics', 'price' => 450000, 'stock' => 5],
    ['id' => 2, 'name' => 'Phone',      'category' => 'electronics', 'price' => 250000, 'stock' => 12],
    ['id' => 3, 'name' => 'Desk Chair', 'category' => 'furniture',   'price' => 85000,  'stock' => 0],
    ['id' => 4, 'name' => 'Notebook',   'category' => 'stationery',  'price' => 1500,   'stock' => 200],
    ['id' => 5, 'name' => 'Pen Pack',   'category' => 'stationery',  'price' => 800,    'stock' => 350],
    ['id' => 6, 'name' => 'Monitor',    'category' => 'electronics', 'price' => 120000, 'stock' => 7],
    ['id' => 7, 'name' => 'Desk',       'category' => 'furniture',   'price' => 150000, 'stock' => 3],
    ['id' => 8, 'name' => 'Headphones', 'category' => 'electronics', 'price' => 35000,  'stock' => 20],
];

// 4. TAGS: flat list with duplicates and mixed case
$tags = ['php', 'vue', 'php', 'python', 'laravel', 'vue', 'mysql', 'php', 'Python'];

// 5. SCORES: plain numbers (one duplicate, one zero)
$scores = [72, 85, 90, 64, 85, 0, 98, 55];


// Quick check so you know the file works
// echo count($users) . " users, " . count($orders) . " orders, " . count($products) . " products\n";
// echo count($scores) . " scores, " . count($tags) . " tags, \n";

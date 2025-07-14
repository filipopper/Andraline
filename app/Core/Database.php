<?php
namespace App\Core;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (!self::$pdo) {
            self::$pdo = new PDO('sqlite:' . DB_PATH);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::migrate();
        }
        return self::$pdo;
    }

    private static function migrate(): void
    {
        $pdo = self::$pdo;

        // Track applied migrations
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $migrations = [
            'create_products' => "CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );",
            'create_users' => "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                name TEXT,
                role TEXT DEFAULT 'customer',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );",
            'create_subscriptions' => "CREATE TABLE IF NOT EXISTS subscriptions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                plan TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'active',
                started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                ends_at DATETIME,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            );",
            'create_wishlists' => "CREATE TABLE IF NOT EXISTS wishlists (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            );",
            'create_wishlist_items' => "CREATE TABLE IF NOT EXISTS wishlist_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                wishlist_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(wishlist_id) REFERENCES wishlists(id) ON DELETE CASCADE,
                FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
            );",
            'add_points_to_users' => "ALTER TABLE users ADD COLUMN points INTEGER DEFAULT 0;",
            'create_badges' => "CREATE TABLE IF NOT EXISTS badges (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                threshold INTEGER NOT NULL
            );",
            'create_user_badges' => "CREATE TABLE IF NOT EXISTS user_badges (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                badge_id INTEGER NOT NULL,
                awarded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(badge_id) REFERENCES badges(id) ON DELETE CASCADE
            );",
            'add_seller_id_to_products' => "ALTER TABLE products ADD COLUMN seller_id INTEGER;",
        ];

        foreach ($migrations as $name => $sql) {
            $stmt = $pdo->prepare('SELECT 1 FROM migrations WHERE name = :name');
            $stmt->execute(['name' => $name]);
            if (!$stmt->fetch()) {
                $pdo->exec($sql);
                $ins = $pdo->prepare('INSERT INTO migrations (name) VALUES (:name)');
                $ins->execute(['name' => $name]);
            }
        }

        // Seed default admin user if none exists
        $adminExists = $pdo->query("SELECT 1 FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();
        if (!$adminExists) {
            $pdo->prepare("INSERT INTO users (email, password, name, role) VALUES ('admin@admin.com', :pwd, 'Administrator', 'admin')")
                ->execute(['pwd' => password_hash('admin', PASSWORD_DEFAULT)]);
        }

        // Seed default badges if table is empty
        $badgeCount = $pdo->query('SELECT COUNT(*) FROM badges')->fetchColumn();
        if ($badgeCount == 0) {
            $pdo->exec("INSERT INTO badges (name, threshold) VALUES
                ('Bronze', 100),
                ('Silver', 500),
                ('Gold', 1000)");
        }
    }
}
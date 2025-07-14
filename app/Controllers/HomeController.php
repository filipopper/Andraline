<?php

namespace Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured products
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, 
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_featured = 1 AND p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT 8
        ");
        $stmt->execute();
        $featuredProducts = $stmt->fetchAll();

        // Get main categories
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            WHERE c.parent_id IS NULL AND c.is_active = 1
            GROUP BY c.id
            ORDER BY c.name
            LIMIT 6
        ");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        // Get latest products
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT 12
        ");
        $stmt->execute();
        $latestProducts = $stmt->fetchAll();

        // Get site settings
        $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_name', 'site_description', 'currency_symbol')");
        $stmt->execute();
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->render('home/index', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
            'settings' => $settings,
            'currentUser' => $this->getCurrentUser()
        ]);
    }
}
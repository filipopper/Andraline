<?php

namespace App\Models;

use Core\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 
        'sort_order', 'is_active', 'meta_title', 'meta_description'
    ];
    
    public static function findBySlug(string $slug): ?self
    {
        $categories = self::where('slug', '=', $slug);
        return !empty($categories) ? $categories[0] : null;
    }
    
    public static function active(): array
    {
        return self::where('is_active', '=', 1);
    }
    
    public static function getTree(): array
    {
        $instance = new static();
        $categories = $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE is_active = 1 ORDER BY sort_order ASC, name ASC"
        );
        
        return self::buildTree($categories);
    }
    
    public static function getRootCategories(): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order ASC, name ASC"
        );
    }
    
    public function getParent(): ?self
    {
        return $this->parent_id ? self::find($this->parent_id) : null;
    }
    
    public function getChildren(): array
    {
        return self::where('parent_id', '=', $this->id);
    }
    
    public function hasChildren(): bool
    {
        return !empty($this->getChildren());
    }
    
    public function getProducts(): array
    {
        return Product::where('category_id', '=', $this->id);
    }
    
    public function getProductCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND status = 'published'",
            [$this->id]
        );
        
        return $result ? $result['count'] : 0;
    }
    
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $category = $this;
        
        while ($category) {
            array_unshift($breadcrumbs, $category);
            $category = $category->getParent();
        }
        
        return $breadcrumbs;
    }
    
    public function getUrl(): string
    {
        return "/categories/{$this->slug}";
    }
    
    public function getImageUrl(): string
    {
        return $this->image ? "/uploads/{$this->image}" : '/assets/images/category-placeholder.jpg';
    }
    
    private static function buildTree(array $categories, int $parentId = null): array
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = self::buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }
}
<?php

namespace Core;

use Core\Database\Database;

abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    
    public function __construct(array $attributes = [])
    {
        $this->db = Database::getInstance();
        $this->attributes = $attributes;
        
        if (!$this->table) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    public static function find(int $id): ?static
    {
        $instance = new static();
        $record = $instance->db->fetchOne(
            "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?",
            [$id]
        );
        
        return $record ? new static($record) : null;
    }
    
    public static function all(): array
    {
        $instance = new static();
        $records = $instance->db->fetchAll("SELECT * FROM {$instance->table}");
        
        return array_map(fn($record) => new static($record), $records);
    }
    
    public static function where(string $column, string $operator, $value): array
    {
        $instance = new static();
        $records = $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?",
            [$value]
        );
        
        return array_map(fn($record) => new static($record), $records);
    }
    
    public static function create(array $data): static
    {
        $instance = new static();
        $filteredData = $instance->filterFillable($data);
        
        $id = $instance->db->insert($instance->table, $filteredData);
        $filteredData[$instance->primaryKey] = $id;
        
        return new static($filteredData);
    }
    
    public function save(): bool
    {
        if ($this->exists()) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    public function update(array $data = []): bool
    {
        if (!empty($data)) {
            $this->fill($data);
        }
        
        $filteredData = $this->filterFillable($this->attributes);
        unset($filteredData[$this->primaryKey]);
        
        $updated = $this->db->update(
            $this->table,
            $filteredData,
            "{$this->primaryKey} = ?",
            [$this->getId()]
        );
        
        return $updated > 0;
    }
    
    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        }
        
        $deleted = $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$this->getId()]
        );
        
        return $deleted > 0;
    }
    
    protected function insert(): bool
    {
        $filteredData = $this->filterFillable($this->attributes);
        unset($filteredData[$this->primaryKey]);
        
        $id = $this->db->insert($this->table, $filteredData);
        $this->attributes[$this->primaryKey] = $id;
        
        return true;
    }
    
    protected function exists(): bool
    {
        return isset($this->attributes[$this->primaryKey]);
    }
    
    protected function getId(): ?int
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }
    
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    public function fill(array $data): void
    {
        $filteredData = $this->filterFillable($data);
        $this->attributes = array_merge($this->attributes, $filteredData);
    }
    
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
    
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }
    
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }
    
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
    
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
    
    public function toArray(): array
    {
        return $this->attributes;
    }
    
    public function toJson(): string
    {
        return json_encode($this->attributes);
    }
}
<?php

namespace Core\Database;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    
    private function __construct()
    {
        $this->connect();
    }
    
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect(): void
    {
        try {
            $dbPath = __DIR__ . '/../../storage/database.sqlite';
            $dbDir = dirname($dbPath);
            
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->connection = new PDO(
                "sqlite:$dbPath",
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Enable foreign key constraints
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }
    
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }
    
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return (int) $this->connection->lastInsertId();
    }
    
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params)->rowCount();
    }
    
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }
    
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }
    
    public function commit(): bool
    {
        return $this->connection->commit();
    }
    
    public function rollback(): bool
    {
        return $this->connection->rollback();
    }
    
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
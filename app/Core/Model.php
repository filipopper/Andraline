<?php
namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    protected static function db(): PDO
    {
        return Database::pdo();
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM ' . static::$table)->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find(int $id): ?object
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn(string $c) => ':' . $c, $columns);
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = self::db()->prepare($sql);
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $sets = array_map(fn(string $c) => "$c = :$c", array_keys($data));
        $data['id'] = $id;
        $sql = 'UPDATE ' . static::$table . ' SET ' . implode(',', $sets) . ' WHERE ' . static::$primaryKey . ' = :id';
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($data);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id');
        return $stmt->execute(['id' => $id]);
    }
}
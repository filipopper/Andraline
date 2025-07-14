<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function createUser(string $email, string $password, ?string $name = null, string $role = 'customer'): int
    {
        return parent::create([
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name'     => $name,
            'role'     => $role,
        ]);
    }

    public static function verifyCredentials(string $email, string $password): ?object
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
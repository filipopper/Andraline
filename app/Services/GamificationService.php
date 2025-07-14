<?php
namespace App\Services;

use App\Models\User;
use App\Core\Model;
use PDO;

class GamificationService
{
    public static function awardPoints(int $userId, int $points, string $reason = ''): void
    {
        $pdo = Model::db();
        $pdo->prepare('UPDATE users SET points = points + :p WHERE id = :id')->execute(['p' => $points, 'id' => $userId]);

        // Check for new badges
        $stmt = $pdo->prepare('SELECT points FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $total = (int) $stmt->fetchColumn();

        $badges = $pdo->query('SELECT id, threshold FROM badges')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($badges as $b) {
            if ($total >= $b['threshold']) {
                $already = $pdo->prepare('SELECT 1 FROM user_badges WHERE user_id = :uid AND badge_id = :bid');
                $already->execute(['uid' => $userId, 'bid' => $b['id']]);
                if (!$already->fetch()) {
                    $pdo->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (:uid, :bid)')->execute(['uid' => $userId, 'bid' => $b['id']]);
                }
            }
        }
    }
}
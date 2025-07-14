<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;

class WishlistController
{
    private function userWishlistId(int $userId): int
    {
        $pdo = Wishlist::db();
        $stmt = $pdo->prepare('SELECT id FROM wishlists WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $userId]);
        $id = $stmt->fetchColumn();
        if (!$id) {
            $id = Wishlist::createForUser($userId);
        }
        return (int) $id;
    }

    public function my(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        if (!$uid) {
            header('Location: /login');
            exit;
        }
        $wid    = $this->userWishlistId($uid);
        $items  = Wishlist::items($wid);
        $w      = Wishlist::find($wid);
        $shareLink = '/wishlist/view?token=' . $w->token;
        View::make('wishlist/my', compact('items', 'shareLink'));
    }

    public function add(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        if (!$uid) {
            header('Location: /login');
            exit;
        }
        $pid = (int) ($_GET['id'] ?? 0);
        if ($pid) {
            $wid = $this->userWishlistId($uid);
            Wishlist::addProduct($wid, $pid);
        }
        header('Location: /wishlist');
    }

    public function share(): void
    {
        // redirect to my to display share link
        header('Location: /wishlist');
    }

    public function view(): void
    {
        $token = $_GET['token'] ?? '';
        $wishlist = Wishlist::findByToken($token);
        if (!$wishlist) {
            echo 'Invalid wishlist';
            return;
        }
        $items = Wishlist::items($wishlist->id);
        View::make('wishlist/view', compact('items'));
    }
}
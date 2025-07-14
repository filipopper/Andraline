<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Subscription;
use App\Services\StripeService;

class SubscriptionController
{
    private string $priceId = 'price_12345'; // TODO: set your Stripe price ID here

    public function plans(): void
    {
        // In real app, fetch plans from DB or config. For now single plan.
        View::make('subscription/plans');
    }

    public function checkout(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        if (!$uid) {
            header('Location: /login');
            exit;
        }
        $successUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/subscription/success';
        $cancelUrl  = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/subscription/cancel';

        $url = StripeService::createSubscriptionSession($this->priceId, $successUrl, $cancelUrl);

        // Create pending subscription record
        Subscription::create([
            'user_id' => $uid,
            'plan'    => 'default',
            'status'  => 'pending',
        ]);

        header('Location: ' . $url);
    }

    public function success(): void
    {
        // For demonstration, mark subscription active.
        $uid = $_SESSION['user']['id'] ?? 0;
        if ($uid) {
            $sub = Subscription::activeForUser($uid);
            if (!$sub) {
                // Update latest pending subscription
                $pdo = Subscription::db();
                $pdo->prepare("UPDATE subscriptions SET status='active' WHERE user_id = :uid ORDER BY id DESC LIMIT 1")
                    ->execute(['uid' => $uid]);
            }
        }
        \App\Services\GamificationService::awardPoints($uid, 50, 'subscription');
        View::make('subscription/success');
    }

    public function cancel(): void
    {
        View::make('subscription/cancel');
    }
}
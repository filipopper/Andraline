<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    private static bool $init = false;

    private static function init(): void
    {
        if (!self::$init) {
            $secret = getenv('STRIPE_SECRET_KEY') ?: 'sk_test_12345'; // Placeholder
            Stripe::setApiKey($secret);
            self::$init = true;
        }
    }

    /**
     * Create a Stripe Checkout session for subscription or one-time product.
     *
     * @param string $priceId Stripe price identifier configured in dashboard.
     * @param string $successUrl URL user is sent after success.
     * @param string $cancelUrl URL user is sent if they cancel.
     * @return string Redirect URL to Stripe-hosted checkout.
     */
    public static function createSubscriptionSession(string $priceId, string $successUrl, string $cancelUrl): string
    {
        self::init();
        $session = Session::create([
            'mode' => 'subscription',
            'line_items' => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
        ]);
        return $session->url;
    }
}
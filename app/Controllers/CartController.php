<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        $totals = $cart->getTotals();
        
        // Remove any unavailable items
        $removedItems = $cart->removeUnavailableItems();
        
        if (!empty($removedItems)) {
            $itemNames = array_column($removedItems, 'name');
            $this->flash('warning', 'Some items were removed from your cart as they are no longer available: ' . implode(', ', $itemNames));
        }
        
        $this->view('cart/index', [
            'cart' => $cart,
            'items' => $items,
            'totals' => $totals,
            'page_title' => 'Shopping Cart'
        ]);
    }
    
    public function add(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $productId = (int)$this->input('product_id');
        $quantity = (int)$this->input('quantity', 1);
        $variantId = $this->input('variant_id') ? (int)$this->input('variant_id') : null;
        
        if ($quantity <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid quantity'], 400);
        }
        
        $product = Product::find($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        
        $cart = $this->getCart();
        
        if ($cart->addItem($productId, $quantity, $variantId)) {
            $this->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => $cart->getItemCount(),
                'cart_total' => $cart->getSubtotal()
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Unable to add product to cart. It may be out of stock'], 400);
        }
    }
    
    public function update(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $itemId = (int)$this->input('item_id');
        $quantity = (int)$this->input('quantity');
        
        if ($quantity < 0) {
            $this->json(['success' => false, 'message' => 'Invalid quantity'], 400);
        }
        
        $cart = $this->getCart();
        
        if ($cart->updateItemQuantity($itemId, $quantity)) {
            $totals = $cart->getTotals();
            
            $this->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => $cart->getItemCount(),
                'totals' => $totals
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Unable to update cart item'], 400);
        }
    }
    
    public function remove(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $itemId = (int)$this->input('item_id');
        $cart = $this->getCart();
        
        if ($cart->removeItem($itemId)) {
            $totals = $cart->getTotals();
            
            $this->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $cart->getItemCount(),
                'totals' => $totals
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Unable to remove item'], 400);
        }
    }
    
    public function clear(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $cart = $this->getCart();
        $cart->clear();
        
        $this->json([
            'success' => true,
            'message' => 'Cart cleared',
            'cart_count' => 0,
            'totals' => [
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'total' => 0
            ]
        ]);
    }
    
    private function getCart(): Cart
    {
        $userId = null;
        if (!$this->isGuest()) {
            $user = $this->auth();
            $userId = $user['id'];
        }
        
        $sessionId = session_id();
        return Cart::getForUser($userId, $sessionId);
    }
}
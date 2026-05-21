<?php

namespace App\Http\Controllers;

use App\Mail\AccountCreatedMail;
use App\Models\Order;
use App\Models\User;
use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function registerFromCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'password' => $validated['password'],
            'last_login_at' => now(),
        ]);

        Mail::to($user->email)->send(new AccountCreatedMail($user));

        Auth::login($user);
        $request->session()->regenerate();

        $order = $this->createOrderFromCart($request, $user->name, $user->email, $validated['phone']);

        if ($order) {
            $request->session()->put('checkout_order_id', $order->id);
        }

        return redirect()
            ->route('account.dashboard')
            ->with('success', 'Account created. Choose M-Pesa payment or WhatsApp order confirmation.');
    }

    public function checkout(Request $request): View
    {
        $order = $this->currentCheckoutOrder($request);
        $cart = $this->cartItems($request);
        $total = $order ? (float) $order->amount : (float) $cart->sum('line_total');

        return view('account.checkout', [
            'order' => $order,
            'items' => $cart,
            'total' => $total,
            'mpesaDialUrl' => 'tel:*334%23',
            'whatsappUrl' => route('shop.cart.whatsapp'),
            'seo' => [
                'title' => 'My Account Checkout | '.SeoData::siteName(),
                'description' => 'Complete your Starlink Kenya order with M-Pesa or WhatsApp.',
                'canonical' => route('account.checkout'),
                'robots' => 'noindex,nofollow',
            ],
        ]);
    }

    private function createOrderFromCart(Request $request, string $name, string $email, string $phone): ?Order
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return null;
        }

        return Order::query()->create([
            'order_number' => $this->newOrderNumber(),
            'customer_name' => $name,
            'customer_email' => $email,
            'phone' => $phone,
            'amount' => (float) $items->sum('line_total'),
            'status' => 'pending',
        ]);
    }

    private function currentCheckoutOrder(Request $request): ?Order
    {
        $orderId = $request->session()->get('checkout_order_id');

        if ($orderId) {
            return Order::query()
                ->whereKey($orderId)
                ->where('customer_email', Auth::user()?->email)
                ->first();
        }

        return Order::query()
            ->where('customer_email', Auth::user()?->email)
            ->latest()
            ->first();
    }

    private function cartItems(Request $request)
    {
        return collect(array_values($request->session()->get('cart', [])))
            ->map(function (array $item): array {
                $item['line_total'] = (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);

                return $item;
            });
    }

    private function newOrderNumber(): string
    {
        do {
            $orderNumber = 'SKI-'.now()->format('ymd').'-'.random_int(1000, 9999);
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}

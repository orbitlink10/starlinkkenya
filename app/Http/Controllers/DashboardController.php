<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (! Auth::user()?->isAdmin()) {
            return redirect()->route('account.dashboard');
        }

        $stats = [
            'orders' => Order::count(),
            'invoices' => Invoice::count(),
            'users' => User::count(),
            'enquiries' => Enquiry::count(),
            'totalRevenue' => Order::whereNotNull('paid_at')->sum('amount'),
            'recentOrders' => Order::where('created_at', '>=', now()->subDays(7))->count(),
            'newUsers' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'activeUsers' => User::where('last_login_at', '>=', now()->subDay())->count(),
        ];

        return view('dashboard.index', [
            'stats' => $stats,
            'seo' => [
                'title' => 'Dashboard | '.SeoData::siteName(),
                'description' => 'Internal dashboard for Starlink Kenya.',
                'canonical' => route('dashboard'),
                'robots' => 'noindex,nofollow',
            ],
        ]);
    }
}

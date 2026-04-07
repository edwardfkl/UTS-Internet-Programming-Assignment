<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::query()->count(),
            'productCount' => Product::query()->count(),
            'orderCount' => Order::query()->count(),
            'pendingPaymentCount' => Order::query()
                ->where('status', Order::STATUS_PENDING_PAYMENT)
                ->count(),
        ]);
    }
}

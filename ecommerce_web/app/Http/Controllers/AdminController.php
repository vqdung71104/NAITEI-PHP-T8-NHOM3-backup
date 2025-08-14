<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'message' => 'Welcome to admin dashboard!'
        ]);
    }

    /**
     * Display admin statistics or overview
     */
    public function index()
    {
        // Example overview data; replace with real statistics as needed
        $overviewData = [
            'totalUsers' => 150,
            'totalOrders' => 320,
            'pendingTasks' => 12,
        ];
        return Inertia::render('Admin/Overview', [
            'overview' => $overviewData,
            'message' => 'Admin overview page'
        ]);
    }
}
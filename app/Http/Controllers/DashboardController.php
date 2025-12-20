<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellTest;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch key metrics for the dashboard
        $totalTests = WellTest::count();
        $totalUsers = User::count();
        $recentTests = WellTest::where('created_at', '>=', now()->subDays(7))->count();

        // Data for the pie chart (Tests by Client)
        $testsByClient = WellTest::selectRaw('client, count(*) as count')
            ->whereNotNull('client')
            ->groupBy('client')
            ->orderBy('count', 'desc')
            ->take(5) // Limit to top 5 clients
            ->get();

        // Data for the line chart (Tests over the last 30 days)
        $testsOverTime = WellTest::selectRaw('DATE(created_at) as date, count(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('dashboard', compact('totalTests', 'totalUsers', 'recentTests', 'testsByClient', 'testsOverTime'));
    }
}

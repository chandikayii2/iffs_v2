<?php
// app/Http/Controllers/Tire/TireDashboardController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\Vehicle;
use App\Models\RefillingOrder;
use App\Models\TireAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TireDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tires' => Tire::count(),
            'new_tires' => Tire::where('status', 'new')->count(),
            'in_use_tires' => Tire::where('status', 'in_use')->count(),
            'used_tires' => Tire::where('status', 'used')->count(),
            'at_vendor_tires' => Tire::where('status', 'at_vendor')->count(),
            'scrap_tires' => Tire::where('status', 'scrap')->count(),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'pending_refilling' => RefillingOrder::where('status', 'sent')->count(),
        ];

        $recentAllocations = TireAllocation::with(['tire', 'vehicle'])
            ->orderBy('installation_date', 'desc')
            ->limit(10)
            ->get();

        $recentTires = Tire::orderBy('created_at', 'desc')->limit(5)->get();

        return view('tire.dashboard', compact('stats', 'recentAllocations', 'recentTires'));
    }
    
    // Get Alerts for dashboard
    public function getAlerts()
    {
        // Tires that need refill soon
        $needsRefill = Tire::where('status', 'used')
            ->whereRaw('refill_count >= max_refills - 1')
            ->get();
        
        // Tires that are old (more than 3 years)
        $oldTires = Tire::where('purchase_date', '<=', now()->subYears(3))
            ->whereNotIn('status', ['scrap'])
            ->get();
        
        // Vehicles with low tire count
        $vehiclesLowTires = Vehicle::withCount(['currentTires'])
            ->having('current_tires_count', '<', 6)
            ->get();
        
        return view('tire.alerts', compact('needsRefill', 'oldTires', 'vehiclesLowTires'));
    }
    
    // Get monthly activity data for charts
    public function getMonthlyActivity(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Get allocations per month
        $allocations = TireAllocation::select(
                DB::raw('MONTH(installation_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('installation_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Get scraps per month
        $scraps = \App\Models\TireScrapRecord::select(
                DB::raw('MONTH(scrap_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('scrap_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Prepare data for all 12 months
        $allocationsData = [];
        $scrapsData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($i = 1; $i <= 12; $i++) {
            $allocationsData[] = $allocations[$i] ?? 0;
            $scrapsData[] = $scraps[$i] ?? 0;
        }
        
        return response()->json([
            'months' => $months,
            'allocations' => $allocationsData,
            'scraps' => $scrapsData
        ]);
    }
    
    public function analytics()
    {
        $stats = [
            'total_tires' => Tire::count(),
            'new_tires' => Tire::where('status', 'new')->count(),
            'in_use_tires' => Tire::where('status', 'in_use')->count(),
            'used_tires' => Tire::where('status', 'used')->count(),
            'at_vendor_tires' => Tire::where('status', 'at_vendor')->count(),
            'scrap_tires' => Tire::where('status', 'scrap')->count(),
        ];
        
        $brandStats = Tire::select('brand', DB::raw('count(*) as count'))
            ->groupBy('brand')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $sizeStats = Tire::select('size', DB::raw('count(*) as count'))
            ->groupBy('size')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $refillStats = Tire::select('refill_count', DB::raw('count(*) as count'))
            ->groupBy('refill_count')
            ->orderBy('refill_count')
            ->get();
        
        return view('tire.reports.analytics', compact('stats', 'brandStats', 'sizeStats', 'refillStats'));
    }
}
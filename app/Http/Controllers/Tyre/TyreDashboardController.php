<?php
// app/Http/Controllers/Tyre/TyreDashboardController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\Vehicle;
use App\Models\RefillingOrder;
use App\Models\TyreAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class TyreDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tyres' => Tyre::count(),
            'new_tyres' => Tyre::where('status', 'new')->where('tire_type', '!=', 'original_casing')->count(),
            'in_use_tyres' => Tyre::where('status', 'in_use')->count(),
            'new_dag_tyres' => Tyre::where('status', 'used')->where('is_refilled', true)->count(),
            'can_use_as_it_is' => Tyre::where('status', 'used')->where('is_refilled', false)->count(),
            'to_be_send_to_dag' => Tyre::where(function($q) {
                $q->where('status', 'at_vendor')
                  ->orWhere(function($sub) {
                      $sub->where('status', 'new')
                          ->where('tire_type', 'original_casing');
                  });
            })->count(),
            'scrap_tyres' => Tyre::where('status', 'scrap')->count(),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'pending_refilling' => RefillingOrder::where('status', 'sent')->count(),
            
            // Subcategories counts
            'brand_new_0' => Tyre::where('refill_count', 0)->where('status', '!=', 'scrap')->count(),
            'round_1' => Tyre::where('refill_count', 1)->where('status', '!=', 'scrap')->count(),
            'round_2' => Tyre::where('refill_count', 2)->where('status', '!=', 'scrap')->count(),
            'round_3' => Tyre::where('refill_count', 3)->where('status', '!=', 'scrap')->count(),
            'round_4' => Tyre::where('refill_count', 4)->where('status', '!=', 'scrap')->count(),
            'round_5' => Tyre::where('refill_count', 5)->where('status', '!=', 'scrap')->count(),
        ];

        $recentAllocations = TyreAllocation::with(['tyre', 'vehicle'])
            ->orderBy('installation_date', 'desc')
            ->limit(10)
            ->get();

        $recentTyres = Tyre::orderBy('created_at', 'desc')->limit(5)->get();

        return view('tyre.dashboard', compact('stats', 'recentAllocations', 'recentTyres'));
    }
    
    // Get Alerts for dashboard
    public function getAlerts()
    {
        // Tyres that need refill soon
        $needsRefill = Tyre::where('status', 'used')
            ->whereRaw('refill_count >= max_refills - 1')
            ->get();
        
        // Tyres that are old (more than 3 years)
        $oldTyres = Tyre::where('purchase_date', '<=', now()->subYears(3))
            ->whereNotIn('status', ['scrap'])
            ->get();
        
        // Vehicles with low tyre count
        $vehiclesLowTyres = Vehicle::withCount(['currentTyres'])
            ->having('current_tyres_count', '<', 6)
            ->get();
        
        return view('tyre.alerts', compact('needsRefill', 'oldTyres', 'vehiclesLowTyres'));
    }
    
    // Get monthly activity data for charts
    public function getMonthlyActivity(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Get allocations per month
        $allocations = TyreAllocation::select(
                DB::raw('MONTH(installation_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('installation_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Get scraps per month
        $scraps = \App\Models\TyreScrapRecord::select(
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
            'total_tyres' => Tyre::count(),
            'new_tyres' => Tyre::where('status', 'new')->count(),
            'in_use_tyres' => Tyre::where('status', 'in_use')->count(),
            'used_tyres' => Tyre::where('status', 'used')->count(),
            'at_vendor_tyres' => Tyre::where('status', 'at_vendor')->count(),
            'scrap_tyres' => Tyre::where('status', 'scrap')->count(),
        ];
        
        $brandStats = Tyre::select('brand', DB::raw('count(*) as count'))
            ->groupBy('brand')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $sizeStats = Tyre::select('size', DB::raw('count(*) as count'))
            ->groupBy('size')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        $refillStats = Tyre::select('refill_count', DB::raw('count(*) as count'))
            ->groupBy('refill_count')
            ->orderBy('refill_count')
            ->get();
        
        return view('tyre.reports.analytics', compact('stats', 'brandStats', 'sizeStats', 'refillStats'));
    }

    public function breakdown(Request $request)
    {
        $category = (int)$request->get('refill_count', 0);
        
        $tyres = Tyre::with(['currentAllocation.vehicle'])->where('refill_count', $category)
            ->where('status', '!=', 'scrap')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $tyres->appends(['refill_count' => $category]);

        // Find the maximum refill count dynamically
        $maxRefillInDb = Tyre::where('status', '!=', 'scrap')->max('refill_count') ?? 0;
        $maxRounds = max(5, $maxRefillInDb);

        $stats = [];
        $stats['brand_new_0'] = Tyre::where('refill_count', 0)->where('status', '!=', 'scrap')->count();
        for ($i = 1; $i <= $maxRounds; $i++) {
            $stats['round_' . $i] = Tyre::where('refill_count', $i)->where('status', '!=', 'scrap')->count();
        }

        return view('tyre.breakdown', compact('stats', 'tyres', 'category', 'maxRounds'));
    }

    public function categoryTyres(Request $request, $type)
    {
        $query = Tyre::with(['currentAllocation.vehicle'])->where('status', '!=', 'scrap');
        $title = '';
        $description = '';
        
        switch ($type) {
            case 'new':
                $query->where('status', 'new')->where('tire_type', '!=', 'original_casing');
                $title = 'New Tyres List';
                $description = 'View and manage brand new tyres in inventory';
                break;
            case 'new_dag':
                $query->where('status', 'used')->where('is_refilled', true);
                $title = 'New Dag Tyres List';
                $description = 'View and manage refilled (retreaded) tyres in inventory';
                break;
            case 'casing':
                $query->where('status', 'used')->where('is_refilled', false);
                $title = 'Can Use As It Is Tyres List';
                $description = 'View and manage used tyres ready for immediate allocation (no retread needed)';
                break;
            case 'at_vendor':
                $query->where(function($q) {
                    $q->where('status', 'at_vendor')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'new')
                              ->where('tire_type', 'original_casing');
                      });
                });
                $title = 'To Be Send to Dag Tyres List';
                $description = 'View and manage tyres currently at vendor for refilling/retreading';
                break;
            default:
                abort(404);
        }
        
        $tyres = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('tyre.category_list', compact('tyres', 'title', 'description', 'type'));
    }

    public function breakdownPdf()
    {
        $maxRefillInDb = Tyre::where('status', '!=', 'scrap')->max('refill_count') ?? 0;
        $maxRounds = max(5, $maxRefillInDb);
        
        $groupedTyres = [];
        // Brand New (0 refills)
        $groupedTyres[0] = Tyre::with(['currentAllocation.vehicle'])->where('refill_count', 0)
            ->where('status', '!=', 'scrap')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Rounds 1 to maxRounds
        for ($i = 1; $i <= $maxRounds; $i++) {
            $groupedTyres[$i] = Tyre::with(['currentAllocation.vehicle'])->where('refill_count', $i)
                ->where('status', '!=', 'scrap')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $pdf = PDF::loadView('tyre.breakdown_pdf', compact('groupedTyres', 'maxRounds'));
        return $pdf->download('tyre_breakdown_report_' . date('d_m_Y') . '.pdf');
    }

    public function categoryPdf(Request $request, $type)
    {
        $query = Tyre::with(['currentAllocation.vehicle'])->where('status', '!=', 'scrap');
        $title = '';
        
        switch ($type) {
            case 'new':
                $query->where('status', 'new')->where('tire_type', '!=', 'original_casing');
                $title = 'New Tyres List';
                break;
            case 'new_dag':
                $query->where('status', 'used')->where('is_refilled', true);
                $title = 'New Dag Tyres List';
                break;
            case 'casing':
                $query->where('status', 'used')->where('is_refilled', false);
                $title = 'Can Use As It Is Tyres List';
                break;
            case 'at_vendor':
                $query->where(function($q) {
                    $q->where('status', 'at_vendor')
                      ->orWhere(function($sub) {
                          $sub->where('status', 'new')
                              ->where('tire_type', 'original_casing');
                      });
                });
                $title = 'To Be Send to Dag Tyres List';
                break;
            default:
                abort(404);
        }
        
        $tyres = $query->orderBy('created_at', 'desc')->get();
        
        $pdf = PDF::loadView('tyre.category_pdf', compact('tyres', 'title', 'type'));
        return $pdf->download(str_replace(' ', '_', strtolower($title)) . '_' . date('d_m_Y') . '.pdf');
    }
}
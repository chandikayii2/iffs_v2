<?php
// app/Http/Controllers/Tire/TireScrapController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\TireScrapRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TireScrapController extends Controller
{
    public function index()
    {
        $scrapTires = Tire::with(['scrapRecord'])
            ->where('status', 'scrap')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_scrap' => Tire::where('status', 'scrap')->count(),
            'scrap_this_month' => Tire::where('status', 'scrap')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'avg_life_km' => TireScrapRecord::avg('final_mileage')
        ];
        
        return view('tire.scrap.index', compact('scrapTires', 'stats'));
    }

    public function scrapTire($tireId)
    {
        $tire = Tire::with(['currentAllocation.vehicle'])->findOrFail($tireId);
        
        return view('tire.scrap.create', compact('tire'));
    }

    public function processScrap(Request $request, $tireId)
    {
        $validator = Validator::make($request->all(), [
            'scrap_reason' => 'required|string',
            'final_mileage' => 'nullable|integer|min:0',
            'disposal_method' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tire = Tire::findOrFail($tireId);
            
            // If tire is currently allocated, remove it first
            if ($tire->currentAllocation) {
                $allocation = $tire->currentAllocation;
                $allocation->mileage_at_removal = $request->final_mileage ?? $allocation->vehicle->current_mileage;
                $allocation->removal_date = now()->toDateString();
                $allocation->removal_reason = 'Scrapped - ' . $request->scrap_reason;
                $allocation->save();
            }
            
            // Create scrap record
            TireScrapRecord::create([
                'tire_id' => $tireId,
                'scrap_date' => now()->toDateString(),
                'scrap_reason' => $request->scrap_reason,
                'final_mileage' => $request->final_mileage,
                'disposal_method' => $request->disposal_method,
                'notes' => $request->notes
            ]);
            
            // Update tire status
            $tire->status = 'scrap';
            $tire->current_location = 'scrap_yard';
            $tire->save();

            DB::commit();

            return redirect()->route('tire.scrap.index')
                ->with('success', 'Tire marked as scrap successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to scrap tire: ' . $e->getMessage());
        }
    }

    public function scrapReport()
    {
        $scrapRecords = TireScrapRecord::with(['tire'])
            ->orderBy('scrap_date', 'desc')
            ->paginate(30);
        
        $summary = [
            'by_reason' => TireScrapRecord::selectRaw('scrap_reason, count(*) as count')
                ->groupBy('scrap_reason')
                ->get(),
            'by_month' => TireScrapRecord::selectRaw('DATE_FORMAT(scrap_date, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get()
        ];
        
        return view('tire.scrap.report', compact('scrapRecords', 'summary'));
    }
}
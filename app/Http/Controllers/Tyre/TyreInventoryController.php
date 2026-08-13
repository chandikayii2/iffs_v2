<?php
// app/Http/Controllers/Tyre/TyreInventoryController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\RefillingVendor;
use App\Models\TyreAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;

class TyreInventoryController extends Controller
{
public function index(Request $request)
{
    $query = Tyre::with(['currentAllocation.vehicle', 'scrapRecord', 'vendor']);

    // Search functionality
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('serial_number', 'LIKE', "%{$search}%")
              ->orWhere('brand', 'LIKE', "%{$search}%")
              ->orWhere('size', 'LIKE', "%{$search}%")
              ->orWhere('type', 'LIKE', "%{$search}%")
              ->orWhere('current_location', 'LIKE', "%{$search}%")
              ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                  $vendorQuery->where('name', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('currentAllocation.vehicle', function($vehicleQuery) use ($search) {
                  $vehicleQuery->where('lorry_number', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('allocations', function($allocationQuery) use ($search) {
                  $allocationQuery->whereNull('removal_date')
                      ->whereHas('vehicle', function($vehicleQuery) use ($search) {
                          $vehicleQuery->where('lorry_number', 'LIKE', "%{$search}%");
                      });
              });
        });
    }

    // Status and Refill filters
    if ($request->has('status') && !empty($request->status)) {
        $query->where('status', $request->status);
    }
    if ($request->has('is_refilled') && $request->is_refilled !== '') {
        $query->where('is_refilled', $request->is_refilled);
    }
    if ($request->has('refill_count') && $request->refill_count !== '') {
        $query->where('refill_count', $request->refill_count);
    }

    $tyres = $query->orderBy('created_at', 'desc')->paginate(20);
    
    // Updated Stats
    $stats = [
        'new' => Tyre::where('status', 'new')->count(),
        'in_use' => Tyre::where('status', 'in_use')->count(),
        'used_stock' => Tyre::where('status', 'used')
            ->where('refill_count', 0)
            ->count(),
        'refilled_stock' => Tyre::where('status', 'used')
            ->where('refill_count', '>', 0)
            ->count(),
        'at_vendor' => Tyre::where('status', 'at_vendor')->count(),
        'scrap' => Tyre::where('status', 'scrap')->count(),
    ];

    // Keep all filters in pagination
    $tyres->appends($request->all());

    return view('tyre.inventory.index', compact('tyres', 'stats'));
}

    public function create()
    {
        $brands = Tyre::select('brand')->distinct()->pluck('brand');
        $sizes = Tyre::select('size')->distinct()->pluck('size');
        $types = Tyre::select('type')->distinct()->pluck('type');
        $vendors = RefillingVendor::orderBy('name')->get();
        
        return view('tyre.inventory.create', compact('brands', 'sizes', 'types', 'vendors'));
    }

    public function store(Request $request)
    {
        $rules = [
            'brand' => 'required|string',
            'size' => 'required|string',
            'type' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'vendor_id' => 'nullable|exists:refilling_vendors,id',
            'max_refills' => 'required|integer|min:0',
            'serial_number' => 'required|string',
        ];

        if ($request->input('tyre_config') === 'dag_used') {
            $rules['total_refill_count'] = 'required|integer|min:0';
            $rules['rounds_finished'] = 'required|integer|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Clean each serial number (trim whitespace, remove empty ones)
        $serialInput = $request->input('serial_number');
        $serials = preg_split('/[\n\r,]+/', $serialInput);
        $serialNumbers = [];
        foreach ($serials as $serial) {
            $trimmed = trim($serial);
            if (!empty($trimmed)) {
                $serialNumbers[] = $trimmed;
            }
        }

        if (empty($serialNumbers)) {
            return redirect()->back()
                ->with('error', 'Please enter at least one valid serial number.')
                ->withInput();
        }

        // Check uniqueness in database
        $existing = Tyre::whereIn('serial_number', $serialNumbers)->pluck('serial_number')->toArray();
        if (!empty($existing)) {
            return redirect()->back()
                ->with('error', 'Duplicate serial number(s) detected in system: ' . implode(', ', $existing))
                ->withInput();
        }

        // Also check if there are duplicate serial numbers in the input itself!
        $duplicatesInInput = array_diff_assoc($serialNumbers, array_unique($serialNumbers));
        if (!empty($duplicatesInInput)) {
            return redirect()->back()
                ->with('error', 'Duplicate serial number(s) entered in the input: ' . implode(', ', array_unique($duplicatesInInput)))
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $tyreConfig = $request->input('tyre_config', 'original');
            $status = ($tyreConfig === 'dag_used') ? 'used' : 'new';
            $isRefilled = ($tyreConfig === 'dag_used');
            $totalRefillCount = $isRefilled ? (int)$request->input('total_refill_count', 0) : 0;
            $roundsFinished = $isRefilled ? (int)$request->input('rounds_finished', 0) : 0;
            $roundKms = null;

            if ($isRefilled) {
                $roundKms = [];
                for ($i = 1; $i <= $roundsFinished; $i++) {
                    $roundKms[(string)$i] = (int)$request->input("round_{$i}_km", 0);
                }
            }

            foreach ($serialNumbers as $serialNumber) {
                Tyre::create([
                    'serial_number' => $serialNumber,
                    'brand' => $request->brand,
                    'size' => $request->size,
                    'type' => $request->type,
                    'status' => $status,
                    'refill_count' => $isRefilled ? $roundsFinished : 0,
                    'total_refill_count' => $roundsFinished,
                    'rounds_finished' => $roundsFinished,
                    'round_kms' => $roundKms,
                    'tire_type' => $tyreConfig,
                    'is_refilled' => $isRefilled,
                    'max_refills' => $isRefilled ? $totalRefillCount : $request->max_refills,
                    'purchase_date' => $request->purchase_date,
                    'purchase_price' => $request->purchase_price,
                    'notes' => $request->notes,
                    'vendor_id' => $request->vendor_id,
                    'consumption_mileage' => $roundKms ? array_sum($roundKms) : 0
                ]);
            }

            DB::commit();

            return redirect()->route('tyre.inventory.index')
                ->with('success', count($serialNumbers) . ' tyre(s) added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add tyres: ' . $e->getMessage())->withInput();
        }
    }


    public function show($id)
    {
        $tyre = Tyre::with(['allocations.vehicle', 'refillingOrders.vendor', 'scrapRecord', 'vendor'])
            ->findOrFail($id);
        
        $lifecycleHistory = $this->getLifecycleHistory($tyre);
        
        // Calculate total consumed mileage
        $totalConsumedMileage = $tyre->allocations->sum('consumed_mileage');
        
        return view('tyre.inventory.show', compact('tyre', 'lifecycleHistory', 'totalConsumedMileage'));
    }

public function edit($id)
{
    $tyre = Tyre::with('vendor')->findOrFail($id);
    $brands = Tyre::select('brand')->distinct()->pluck('brand');
    $sizes = Tyre::select('size')->distinct()->pluck('size');
    $types = Tyre::select('type')->distinct()->pluck('type');
    $vendors = RefillingVendor::orderBy('name')->get();
    
    return view('tyre.inventory.edit', compact('tyre', 'brands', 'sizes', 'types', 'vendors'));
}

public function update(Request $request, $id)
{
    $tyre = Tyre::findOrFail($id);
    
    $rules = [
        'serial_number' => 'required|unique:tires,serial_number,' . $id,
        'brand' => 'required|string',
        'size' => 'required|string',
        'type' => 'required|string',
        'purchase_date' => 'required|date',
        'purchase_price' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
        'vendor_id' => 'nullable|exists:refilling_vendors,id',
        'tyre_config' => 'required|string|in:original,original_casing,dag_used'
    ];

    if ($request->input('tyre_config') === 'dag_used') {
        $rules['total_refill_count'] = 'required|integer|min:0';
        $rules['rounds_finished'] = 'required|integer|min:0';
    } else {
        $rules['max_refills'] = 'required|integer|min:' . $tyre->refill_count . '|max:20';
        $rules['consumption_mileage'] = 'nullable|integer|min:0';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        $tyreConfig = $request->input('tyre_config', 'original');
        $isRefilled = ($tyreConfig === 'dag_used');
        $status = $tyre->status;
        if (in_array($status, ['new', 'used'])) {
            $status = $isRefilled ? 'used' : 'new';
        }
        
        $totalRefillCount = $isRefilled ? (int)$request->input('total_refill_count', 0) : (int)$request->input('max_refills', 0);
        $roundsFinished = $isRefilled ? (int)$request->input('rounds_finished', 0) : 0;
        $roundKms = null;
        $consumptionMileage = (int)$request->input('consumption_mileage', 0);

        if ($isRefilled) {
            $roundKms = [];
            for ($i = 1; $i <= $roundsFinished; $i++) {
                $roundKms[(string)$i] = (int)$request->input("round_{$i}_km", 0);
            }
            $consumptionMileage = array_sum($roundKms);
        }

        $tyre->update([
            'serial_number' => $request->serial_number,
            'brand' => $request->brand,
            'size' => $request->size,
            'type' => $request->type,
            'status' => $status,
            'refill_count' => $isRefilled ? $roundsFinished : $tyre->refill_count,
            'total_refill_count' => $isRefilled ? $roundsFinished : $tyre->total_refill_count,
            'rounds_finished' => $roundsFinished,
            'round_kms' => $roundKms,
            'tire_type' => $tyreConfig,
            'is_refilled' => $isRefilled,
            'max_refills' => $totalRefillCount,
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'consumption_mileage' => $consumptionMileage,
            'notes' => $request->notes,
            'vendor_id' => $request->vendor_id
        ]);

        return redirect()->route('tyre.inventory.show', $id)
            ->with('success', 'Tyre updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update tyre: ' . $e->getMessage());
    }
}
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $tyre = Tyre::findOrFail($id);
            
            if (in_array($tyre->status, ['in_use', 'at_vendor'])) {
                DB::rollBack();
                return response()->json([
                    'status' => 400, 
                    'message' => 'Cannot delete tyre that is in use or at vendor!'
                ]);
            }
            
            // Manually delete related records to bypass missing cascade foreign keys in live database
            DB::table('tire_allocations')->where('tire_id', $id)->delete();
            DB::table('refilling_order_items')->where('tire_id', $id)->delete();
            DB::table('tire_scrap_records')->where('tire_id', $id)->delete();
            DB::table('tire_issue_note_items')->where('tire_id', $id)->delete();
            
            $tyre->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 200, 
                'message' => 'Tyre deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500, 
                'message' => 'Failed to delete tyre: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show allocate to vehicle form
     */
    public function allocateToVehicle($id)
    {
        $tyre = Tyre::findOrFail($id);
        
        // Check if tyre can be allocated
        if (!in_array($tyre->status, ['new', 'used'])) {
            return redirect()->route('tyre.inventory.index')
                ->with('error', 'This tyre cannot be allocated (status: ' . $tyre->status . ')');
        }
        
        // Get all active vehicles
        $vehicles = Vehicle::where('status', 'active')->get();
        
        if ($vehicles->isEmpty()) {
            return redirect()->route('tyre.inventory.index')
                ->with('error', 'No active vehicles found. Please add a vehicle first.');
        }
        
        return view('tyre.inventory.allocate', compact('tyre', 'vehicles'));
    }

    /**
     * Process allocate to vehicle
     */
    public function processAllocateToVehicle(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'remark' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tyre = Tyre::findOrFail($id);
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            
            // Check if tyre is already allocated
            if ($tyre->status == 'in_use') {
                throw new \Exception('Tyre ' . $tyre->serial_number . ' is already in use!');
            }
            
            // Check if tyre can be allocated
            if (!in_array($tyre->status, ['new', 'used'])) {
                throw new \Exception('Tyre cannot be allocated (status: ' . $tyre->status . ')');
            }
            
            // Create allocation record
            TyreAllocation::create([
                'tire_id' => $tyre->id,
                'vehicle_id' => $request->vehicle_id,
                'mileage_at_installation' => 0,
                'position' => null,
                'installation_date' => now()->toDateString(),
                'remark' => $request->remark
            ]);

            // Update tyre status
            $tyre->status = 'in_use';
            $tyre->current_location = 'vehicle_' . $request->vehicle_id;
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.vehicles.show', $request->vehicle_id)
                ->with('success', 'Tyre ' . $tyre->serial_number . ' allocated to ' . $vehicle->lorry_number . ' successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to allocate tyre: ' . $e->getMessage());
        }
    }

    public function sendForRefill($id)
    {
        $tyre = Tyre::findOrFail($id);
        
        if (!$tyre->canRefill()) {
            return redirect()->back()->with('error', 'This tyre cannot be refilled (max refills reached or already scrap)!');
        }
        
        // Store tyre ID in session and redirect to refill creation
        session(['selected_tyre_id' => $tyre->id]);
        
        return redirect()->route('tyre.refilling.create')
            ->with('success', 'Selected tyre: ' . $tyre->serial_number . '. Please complete the refill order.');
    }

/**
 * Generate Gate Pass for tyre
 */
public function generateGatePass($id)
{
    $tyre = Tyre::with(['vendor', 'currentAllocation.vehicle'])->findOrFail($id);
    
    // Generate gate pass number
    $gatePassNumber = 'GP-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    
    return view('tyre.inventory.gate_pass', compact('tyre', 'gatePassNumber'));
}

    /**
     * Show remove from vehicle form
     */
    public function removeTyreFromVehicle($tyreId)
    {
        $tyre = Tyre::with(['currentAllocation.vehicle'])->findOrFail($tyreId);
        
        if ($tyre->status != 'in_use') {
            return redirect()->back()->with('error', 'This tyre is not currently in use.');
        }
        
        $currentVehicle = $tyre->currentAllocation->vehicle ?? null;
        
        return view('tyre.inventory.remove_from_vehicle', compact('tyre', 'currentVehicle'));
    }

    /**
     * Process tyre removal from vehicle
     */
    public function processTyreRemoval(Request $request, $tyreId)
    {
        $rules = [
            'consumed_mileage' => 'required|integer|min:0',
            'removal_reason' => 'required|string',
            'action' => 'required|in:store,scrap,send_refill'
        ];

        if ($request->removal_reason === 'Damage') {
            $rules['custom_removal_damage_reason'] = 'required|string';
        } elseif ($request->removal_reason === 'Other') {
            $rules['custom_removal_reason'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tyre = Tyre::findOrFail($tyreId);
            $currentAllocation = $tyre->currentAllocation;
            
            if (!$currentAllocation) {
                throw new \Exception('Tyre is not currently allocated to any vehicle.');
            }
            
            if ($request->removal_reason === 'Damage') {
                $reason = 'Damage: ' . $request->custom_removal_damage_reason;
            } elseif ($request->removal_reason === 'Other') {
                $reason = $request->custom_removal_reason;
            } else {
                $reason = $request->removal_reason;
            }

            // Update allocation with removal details
            $currentAllocation->mileage_at_removal = 0;
            $currentAllocation->removal_date = now()->toDateString();
            $currentAllocation->removal_reason = $reason;
            $currentAllocation->consumed_mileage = $request->consumed_mileage;
            $currentAllocation->save();
            
            // Update tyre consumption mileage
            $tyre->consumption_mileage += $request->consumed_mileage;
            
            // Update tyre status based on action
            switch ($request->action) {
                case 'store':
                    $tyre->status = 'used';
                    $tyre->is_refilled = false;
                    $tyre->current_location = 'store';
                    break;
                case 'scrap':
                    $tyre->status = 'scrap';
                    $tyre->current_location = 'Store';
                    \App\Models\TyreScrapRecord::create([
                        'tire_id' => $tyreId,
                        'scrap_date' => now()->toDateString(),
                        'scrap_reason' => $reason,
                        'final_mileage' => $tyre->consumption_mileage,
                        'disposal_method' => 'Stored',
                        'notes' => 'Scrapped automatically on removal from vehicle.',
                        'scrap_category' => 'store'
                    ]);
                    break;
                case 'send_refill':
                    $tyre->status = 'at_vendor';
                    $tyre->current_location = 'store';
                    break;
            }
            
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.inventory.show', $tyreId)
                ->with('success', 'Tyre removed successfully! Consumed mileage: ' . number_format($request->consumed_mileage) . ' km');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove tyre: ' . $e->getMessage());
        }
    }

    /**
     * Update refill count automatically
     */
    public function updateRefillCount($tyreId, Request $request)
    {
        try {
            $tyre = Tyre::findOrFail($tyreId);
            $increment = $request->input('increment', 1);
            $tyre->refill_count += $increment;
            $tyre->save();
            
            return response()->json([
                'success' => true,
                'refill_count' => $tyre->refill_count,
                'max_refills' => $tyre->max_refills,
                'message' => 'Refill count updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update refill count: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getLifecycleHistory($tyre)
    {
        $history = [];
        
        // Add Purchase event
        $history[] = [
            'date' => $tyre->purchase_date ? $tyre->purchase_date->toDateString() : now()->toDateString(),
            'type' => 'Purchase',
            'details' => "Purchased from " . ($tyre->vendor->name ?? 'Unknown Vendor') . " for Rs. " . number_format($tyre->purchase_price, 2),
            'info' => 'Purchase Price: Rs.' . number_format($tyre->purchase_price, 2)
        ];
        
        // Add Dag Used History (if exists)
        if ($tyre->tire_type === 'dag_used' && !empty($tyre->round_kms)) {
            $roundKms = $tyre->round_kms;
            for ($i = 1; $i <= $tyre->rounds_finished; $i++) {
                $km = $roundKms[$i] ?? $roundKms[(string)$i] ?? null;
                if ($km !== null && $km > 0) {
                    $history[] = [
                        'date' => $tyre->purchase_date ? $tyre->purchase_date->toDateString() : now()->toDateString(),
                        'type' => "Dag Round $i",
                        'details' => "Dag Round $i Consumed: " . number_format($km) . " km",
                        'info' => "R$i Consumed Mileage: " . number_format($km) . " km"
                    ];
                }
            }
        }
        
        // Add allocation history
        foreach ($tyre->allocations as $allocation) {
            $history[] = [
                'date' => $allocation->installation_date,
                'type' => 'Installation',
                'details' => "Installed on {$allocation->vehicle->lorry_number}",
                'mileage' => $allocation->consumed_mileage,
                'remark' => $allocation->remark
            ];
            
            if ($allocation->removal_date) {
                $history[] = [
                    'date' => $allocation->removal_date,
                    'type' => 'Removal',
                    'details' => "Removed from {$allocation->vehicle->lorry_number} - Reason: {$allocation->removal_reason}",
                    'mileage' => $allocation->consumed_mileage
                ];
            }
        }
        
        // Add refilling history
        foreach ($tyre->refillingOrders as $order) {
            $history[] = [
                'date' => $order->sent_date,
                'type' => 'Sent for Refilling',
                'details' => "Sent to {$order->vendor->name}",
                'order_number' => $order->order_number
            ];
            
            if ($order->received_date) {
                $history[] = [
                    'date' => $order->received_date,
                    'type' => 'Received from Refilling',
                    'details' => "Received from {$order->vendor->name}",
                    'cost' => $order->pivot->refilling_cost
                ];
            }
        }
        
        // Add scrap record if exists
        if ($tyre->scrapRecord) {
            $history[] = [
                'date' => $tyre->scrapRecord->scrap_date,
                'type' => 'Scrapped',
                'details' => "Scrapped - Reason: {$tyre->scrapRecord->scrap_reason}",
                'final_mileage' => $tyre->scrapRecord->final_mileage
            ];
        }
        
        // Sort by date
        usort($history, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return $history;
    }
    
    public function searchPassport()
    {
        return view('tyre.passport.search');
    }

    public function lookupTyre(Request $request)
    {
        $tyre = Tyre::where('serial_number', $request->serial_number)->first();
        if ($tyre) {
            return redirect()->route('tyre.inventory.show', $tyre->id);
        }
        return redirect()->back()->with('error', 'Tyre not found!');
    }
    
    // API method to get brand options
    public function getBrands()
    {
        $brands = Tyre::select('brand')->distinct()->pluck('brand');
        return response()->json($brands);
    }
    
    // API method to get size options
    public function getSizes()
    {
        $sizes = Tyre::select('size')->distinct()->pluck('size');
        return response()->json($sizes);
    }
    
    // API method to get type options
    public function getTypes()
    {
        $types = Tyre::select('type')->distinct()->pluck('type');
        return response()->json($types);
    }
    
    public function exportExcel()
    {
        $tyres = Tyre::all();
        // Use Maatwebsite Excel or similar
        // Return Excel file
        return redirect()->back()->with('info', 'Export functionality coming soon');
    }

    public function exportPdf()
    {
        $tyres = Tyre::all();
        // Use PDF library
        return redirect()->back()->with('info', 'PDF export functionality coming soon');
    }

    public function tyreLifeReport()
    {
        $tyres = Tyre::with(['allocations', 'refillingOrders'])->get();
        $averageLife = TyreAllocation::avg('consumed_mileage');
        
        return view('tyre.reports.tyre_life', compact('tires', 'averageLife'));
    }

    public function generatePassportPdf($tyreId)
    {
        $tyre = Tyre::with(['allocations.vehicle', 'refillingOrders.vendor', 'scrapRecord', 'vendor'])
            ->findOrFail($tyreId);
        
        $lifecycleHistory = $this->getLifecycleHistory($tyre);
        
        return view('tyre.passport.pdf', compact('tyre', 'lifecycleHistory'));
    }

    public function printPassport($tyreId)
    {
        $tyre = Tyre::with(['allocations.vehicle', 'refillingOrders.vendor', 'scrapRecord', 'vendor'])
            ->findOrFail($tyreId);
        
        $lifecycleHistory = $this->getLifecycleHistory($tyre);
        
        return view('tyre.passport.pdf', compact('tyre', 'lifecycleHistory'));
    }
}
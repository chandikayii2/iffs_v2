<?php
// app/Http/Controllers/Tire/TireInventoryController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\RefillingVendor;
use App\Models\TireAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;

class TireInventoryController extends Controller
{
    public function index()
    {
        $tires = Tire::with(['currentAllocation.vehicle', 'scrapRecord', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'new' => Tire::where('status', 'new')->count(),
            'in_use' => Tire::where('status', 'in_use')->count(),
            'used' => Tire::where('status', 'used')->count(),
            'at_vendor' => Tire::where('status', 'at_vendor')->count(),
            'scrap' => Tire::where('status', 'scrap')->count(),
        ];

        return view('tire.inventory.index', compact('tires', 'stats'));
    }

    public function create()
    {
        $brands = Tire::select('brand')->distinct()->pluck('brand');
        $sizes = Tire::select('size')->distinct()->pluck('size');
        $types = Tire::select('type')->distinct()->pluck('type');
        $vendors = RefillingVendor::orderBy('name')->get();
        
        return view('tire.inventory.create', compact('brands', 'sizes', 'types', 'vendors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required|unique:tires,serial_number',
            'brand' => 'required|string',
            'size' => 'required|string',
            'type' => 'required|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
            'vendor_id' => 'nullable|exists:refilling_vendors,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $quantity = $request->input('quantity', 1);
            
            for ($i = 0; $i < $quantity; $i++) {
                $serialNumber = $request->serial_number;
                if ($quantity > 1) {
                    $serialNumber = $request->serial_number . '-' . ($i + 1);
                }

                Tire::create([
                    'serial_number' => $serialNumber,
                    'brand' => $request->brand,
                    'size' => $request->size,
                    'type' => $request->type,
                    'status' => 'new',
                    'refill_count' => 0,
                    'max_refills' => $request->max_refills ?? 3,
                    'purchase_date' => $request->purchase_date,
                    'purchase_price' => $request->purchase_price,
                    'notes' => $request->notes,
                    'vendor_id' => $request->vendor_id,
                    'consumption_mileage' => 0
                ]);
            }

            DB::commit();

            return redirect()->route('tire.inventory.index')
                ->with('success', $quantity . ' tire(s) added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add tires: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $tire = Tire::with(['allocations.vehicle', 'refillingOrders.vendor', 'scrapRecord', 'vendor'])
            ->findOrFail($id);
        
        $lifecycleHistory = $this->getLifecycleHistory($tire);
        
        // Calculate total consumed mileage
        $totalConsumedMileage = $tire->allocations->sum('consumed_mileage');
        
        return view('tire.inventory.show', compact('tire', 'lifecycleHistory', 'totalConsumedMileage'));
    }

    public function edit($id)
    {
        $tire = Tire::with('vendor')->findOrFail($id);
        $brands = Tire::select('brand')->distinct()->pluck('brand');
        $sizes = Tire::select('size')->distinct()->pluck('size');
        $types = Tire::select('type')->distinct()->pluck('type');
        $vendors = RefillingVendor::orderBy('name')->get();
        
        return view('tire.inventory.edit', compact('tire', 'brands', 'sizes', 'types', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string',
            'size' => 'required|string',
            'type' => 'required|string',
            'max_refills' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'vendor_id' => 'nullable|exists:refilling_vendors,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $tire = Tire::findOrFail($id);
            $tire->update([
                'brand' => $request->brand,
                'size' => $request->size,
                'type' => $request->type,
                'max_refills' => $request->max_refills,
                'notes' => $request->notes,
                'vendor_id' => $request->vendor_id
            ]);

            return redirect()->route('tire.inventory.show', $id)
                ->with('success', 'Tire updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update tire: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $tire = Tire::findOrFail($id);
            
            if (!in_array($tire->status, ['new', 'scrap'])) {
                return response()->json([
                    'status' => 400, 
                    'message' => 'Cannot delete tire that is in use or at vendor!'
                ]);
            }
            
            $tire->delete();
            
            return response()->json([
                'status' => 200, 
                'message' => 'Tire deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500, 
                'message' => 'Failed to delete tire: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show allocate to vehicle form
     */
    public function allocateToVehicle($id)
    {
        $tire = Tire::findOrFail($id);
        
        // Check if tire can be allocated
        if (!in_array($tire->status, ['new', 'used'])) {
            return redirect()->route('tire.inventory.index')
                ->with('error', 'This tire cannot be allocated (status: ' . $tire->status . ')');
        }
        
        // Get all active vehicles
        $vehicles = Vehicle::where('status', 'active')->get();
        
        if ($vehicles->isEmpty()) {
            return redirect()->route('tire.inventory.index')
                ->with('error', 'No active vehicles found. Please add a vehicle first.');
        }
        
        return view('tire.inventory.allocate', compact('tire', 'vehicles'));
    }

    /**
     * Process allocate to vehicle
     */
    public function processAllocateToVehicle(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'position' => 'nullable|string',
            'remark' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tire = Tire::findOrFail($id);
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            
            // Check if tire is already allocated
            if ($tire->status == 'in_use') {
                throw new \Exception('Tire ' . $tire->serial_number . ' is already in use!');
            }
            
            // Check if tire can be allocated
            if (!in_array($tire->status, ['new', 'used'])) {
                throw new \Exception('Tire cannot be allocated (status: ' . $tire->status . ')');
            }
            
            // Create allocation record
            TireAllocation::create([
                'tire_id' => $tire->id,
                'vehicle_id' => $request->vehicle_id,
                'mileage_at_installation' => 0,
                'position' => $request->position,
                'installation_date' => now()->toDateString(),
                'remark' => $request->remark
            ]);

            // Update tire status
            $tire->status = 'in_use';
            $tire->current_location = 'vehicle_' . $request->vehicle_id;
            $tire->save();

            DB::commit();

            return redirect()->route('tire.vehicles.show', $request->vehicle_id)
                ->with('success', 'Tire ' . $tire->serial_number . ' allocated to ' . $vehicle->lorry_number . ' successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to allocate tire: ' . $e->getMessage());
        }
    }

    public function sendForRefill($id)
    {
        $tire = Tire::findOrFail($id);
        
        if (!$tire->canRefill()) {
            return redirect()->back()->with('error', 'This tire cannot be refilled (max refills reached or already scrap)!');
        }
        
        // Store tire ID in session and redirect to refill creation
        session(['selected_tire_id' => $tire->id]);
        
        return redirect()->route('tire.refilling.create')
            ->with('success', 'Selected tire: ' . $tire->serial_number . '. Please complete the refill order.');
    }

/**
 * Generate Gate Pass for tire
 */
public function generateGatePass($id)
{
    $tire = Tire::with(['vendor', 'currentAllocation.vehicle'])->findOrFail($id);
    
    // Generate gate pass number
    $gatePassNumber = 'GP-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    
    return view('tire.inventory.gate_pass', compact('tire', 'gatePassNumber'));
}

    /**
     * Show remove from vehicle form
     */
    public function removeTireFromVehicle($tireId)
    {
        $tire = Tire::with(['currentAllocation.vehicle'])->findOrFail($tireId);
        
        if ($tire->status != 'in_use') {
            return redirect()->back()->with('error', 'This tire is not currently in use.');
        }
        
        $currentVehicle = $tire->currentAllocation->vehicle ?? null;
        
        return view('tire.inventory.remove_from_vehicle', compact('tire', 'currentVehicle'));
    }

    /**
     * Process tire removal from vehicle
     */
    public function processTireRemoval(Request $request, $tireId)
    {
        $validator = Validator::make($request->all(), [
            'consumed_mileage' => 'required|integer|min:0',
            'removal_reason' => 'required|string',
            'action' => 'required|in:store,scrap,send_refill'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $tire = Tire::findOrFail($tireId);
            $currentAllocation = $tire->currentAllocation;
            
            if (!$currentAllocation) {
                throw new \Exception('Tire is not currently allocated to any vehicle.');
            }
            
            // Update allocation with removal details
            $currentAllocation->mileage_at_removal = 0;
            $currentAllocation->removal_date = now()->toDateString();
            $currentAllocation->removal_reason = $request->removal_reason;
            $currentAllocation->consumed_mileage = $request->consumed_mileage;
            $currentAllocation->save();
            
            // Update tire consumption mileage
            $tire->consumption_mileage += $request->consumed_mileage;
            
            // Update tire status based on action
            switch ($request->action) {
                case 'store':
                    $tire->status = 'used';
                    $tire->current_location = 'store';
                    break;
                case 'scrap':
                    $tire->status = 'scrap';
                    $tire->current_location = 'scrap';
                    break;
                case 'send_refill':
                    $tire->status = 'at_vendor';
                    $tire->current_location = 'pending_refill';
                    break;
            }
            
            $tire->save();

            DB::commit();

            return redirect()->route('tire.inventory.show', $tireId)
                ->with('success', 'Tire removed successfully! Consumed mileage: ' . number_format($request->consumed_mileage) . ' km');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove tire: ' . $e->getMessage());
        }
    }

    /**
     * Update refill count automatically
     */
    public function updateRefillCount($tireId, Request $request)
    {
        try {
            $tire = Tire::findOrFail($tireId);
            $increment = $request->input('increment', 1);
            $tire->refill_count += $increment;
            $tire->save();
            
            return response()->json([
                'success' => true,
                'refill_count' => $tire->refill_count,
                'max_refills' => $tire->max_refills,
                'message' => 'Refill count updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update refill count: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getLifecycleHistory($tire)
    {
        $history = [];
        
        // Add allocation history
        foreach ($tire->allocations as $allocation) {
            $history[] = [
                'date' => $allocation->installation_date,
                'type' => 'Installation',
                'details' => "Installed on {$allocation->vehicle->lorry_number}",
                'position' => $allocation->position,
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
        foreach ($tire->refillingOrders as $order) {
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
        if ($tire->scrapRecord) {
            $history[] = [
                'date' => $tire->scrapRecord->scrap_date,
                'type' => 'Scrapped',
                'details' => "Scrapped - Reason: {$tire->scrapRecord->scrap_reason}",
                'final_mileage' => $tire->scrapRecord->final_mileage
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
        return view('tire.passport.search');
    }

    public function lookupTire(Request $request)
    {
        $tire = Tire::where('serial_number', $request->serial_number)->first();
        if ($tire) {
            return redirect()->route('tire.inventory.show', $tire->id);
        }
        return redirect()->back()->with('error', 'Tire not found!');
    }
    
    // API method to get brand options
    public function getBrands()
    {
        $brands = Tire::select('brand')->distinct()->pluck('brand');
        return response()->json($brands);
    }
    
    // API method to get size options
    public function getSizes()
    {
        $sizes = Tire::select('size')->distinct()->pluck('size');
        return response()->json($sizes);
    }
    
    // API method to get type options
    public function getTypes()
    {
        $types = Tire::select('type')->distinct()->pluck('type');
        return response()->json($types);
    }
    
    public function exportExcel()
    {
        $tires = Tire::all();
        // Use Maatwebsite Excel or similar
        // Return Excel file
        return redirect()->back()->with('info', 'Export functionality coming soon');
    }

    public function exportPdf()
    {
        $tires = Tire::all();
        // Use PDF library
        return redirect()->back()->with('info', 'PDF export functionality coming soon');
    }

    public function tireLifeReport()
    {
        $tires = Tire::with(['allocations', 'refillingOrders'])->get();
        $averageLife = TireAllocation::avg('consumed_mileage');
        
        return view('tire.reports.tire_life', compact('tires', 'averageLife'));
    }
}
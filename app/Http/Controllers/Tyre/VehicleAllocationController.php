<?php
// app/Http/Controllers/Tyre/VehicleAllocationController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\Vehicle;
use App\Models\TyreAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VehicleAllocationController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with([
            'currentTyres',
            'tyreAllocations.tyre.vendor',
            'tyreAllocations.tyre.refillingOrders.vendor'
        ])->paginate(20);
        
        return view('tyre.vehicles.index', compact('vehicles'));
    }

    public function createVehicle()
    {
        return view('tyre.vehicles.create');
    }

public function storeVehicle(Request $request)
{
    $validator = Validator::make($request->all(), [
        'lorry_number' => 'required|unique:vehicles,lorry_number',
        'status' => 'required|in:active,inactive,maintenance'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    Vehicle::create([
        'lorry_number' => $request->lorry_number,
        'status' => $request->status,
        'driver_name' => '', // Empty string instead of null
        'driver_contact' => '',
        'current_mileage' => 0
    ]);

    return redirect()->route('tyre.vehicles.index')
        ->with('success', 'Vehicle added successfully!');
}

    public function editVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        return view('tyre.vehicles.edit', compact('vehicle'));
    }

    public function updateVehicle(Request $request, $vehicleId)
    {
        $validator = Validator::make($request->all(), [
            'lorry_number' => 'required|string|unique:vehicles,lorry_number,' . $vehicleId,
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $vehicle->update([
                'lorry_number' => $request->lorry_number,
                'status' => $request->status
            ]);

            return redirect()->route('tyre.vehicles.show', $vehicleId)
                ->with('success', 'Vehicle updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update vehicle: ' . $e->getMessage());
        }
    }

    public function deleteVehicle($vehicleId)
    {
        try {
            $vehicle = Vehicle::findOrFail($vehicleId);
            
            // Check if vehicle has any tyres allocated
            $hasAllocatedTyres = TyreAllocation::where('vehicle_id', $vehicleId)
                ->whereNull('removal_date')
                ->exists();
            
            if ($hasAllocatedTyres) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Cannot delete vehicle with allocated tyres. Please remove all tyres first.'
                ]);
            }
            
            $vehicle->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Vehicle deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete vehicle: ' . $e->getMessage()
            ]);
        }
    }

    public function allocateForm($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $availableTyres = Tyre::where('status', 'new')
            ->orWhere('status', 'used')
            ->get();
        
        return view('tyre.vehicles.allocate', compact('vehicle', 'availableTyres'));
    }

public function allocateTyres(Request $request, $vehicleId)
    {
        $validator = Validator::make($request->all(), [
            'tyre_ids' => 'required|array|min:1',
            'tyre_ids.*' => 'exists:tires,id',
            'positions' => 'nullable|array',
            'positions.*' => 'nullable|string',
            'remark' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $vehicle = Vehicle::findOrFail($vehicleId);

            foreach ($request->tyre_ids as $index => $tyreId) {
                $tyre = Tyre::findOrFail($tyreId);
                
                // Check if tyre is already allocated
                if ($tyre->status == 'in_use') {
                    throw new \Exception('Tyre ' . $tyre->serial_number . ' is already in use!');
                }
                
                // Create allocation record
                TyreAllocation::create([
                    'tire_id' => $tyreId,
                    'vehicle_id' => $vehicleId,
                    'mileage_at_installation' => 0,
                    'position' => isset($request->positions[$index]) ? $request->positions[$index] : null,
                    'installation_date' => now()->toDateString(),
                    'remark' => $request->remark
                ]);

                // Update tyre status
                $tyre->status = 'in_use';
                $tyre->current_location = 'vehicle_' . $vehicleId;
                $tyre->save();
            }

            DB::commit();

            return redirect()->route('tyre.vehicles.show', $vehicleId)
                ->with('success', 'Tyre(s) allocated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to allocate tyres: ' . $e->getMessage());
        }
    }

    public function removeTyre($allocationId)
    {
        $allocation = TyreAllocation::with(['tyre', 'vehicle'])->findOrFail($allocationId);
        
        return view('tyre.vehicles.remove', compact('allocation'));
    }

    public function processRemoval(Request $request, $allocationId)
    {
        $validator = Validator::make($request->all(), [
            'removal_reason' => 'required|string',
            'action' => 'required|in:store,scrap,send_refill'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $allocation = TyreAllocation::with('tyre')->findOrFail($allocationId);
            
            // Update allocation with removal details
            $allocation->mileage_at_removal = 0;
            $allocation->removal_date = now()->toDateString();
            $allocation->removal_reason = $request->removal_reason;
            $allocation->save();

            // Update tyre status based on action
            $tyre = $allocation->tyre;
            
            switch ($request->action) {
                case 'store':
                    $tyre->status = 'used';
                    $tyre->current_location = 'store';
                    break;
                case 'scrap':
                    $tyre->status = 'scrap';
                    $tyre->current_location = 'scrap';
                    break;
                case 'send_refill':
                    $tyre->status = 'at_vendor';
                    $tyre->current_location = 'pending_refill';
                    break;
            }
            
            $tyre->save();

            DB::commit();

            return redirect()->route('tyre.vehicles.show', $allocation->vehicle_id)
                ->with('success', 'Tyre removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove tyre: ' . $e->getMessage());
        }
    }

    public function showVehicle($vehicleId)
    {
        $vehicle = Vehicle::with(['tyreAllocations' => function($q) {
            $q->with(['tyre']);
        }])->findOrFail($vehicleId);
        
        $currentTyres = $vehicle->tyreAllocations()->whereNull('removal_date')->get();
        $historyTyres = $vehicle->tyreAllocations()->whereNotNull('removal_date')->get();
        
        return view('tyre.vehicles.show', compact('vehicle', 'currentTyres', 'historyTyres'));
    }

    public function getAvailableTyres()
    {
        $availableTyres = Tyre::where('status', 'new')
            ->orWhere('status', 'used')
            ->select('id', 'serial_number', 'brand', 'size', 'status')
            ->get();
        
        return response()->json($availableTyres);
    }

    public function getCurrentTyres($vehicleId)
    {
        $currentTyres = TyreAllocation::where('vehicle_id', $vehicleId)
            ->whereNull('removal_date')
            ->with('tyre')
            ->get();
        
        return response()->json($currentTyres);
    }
}
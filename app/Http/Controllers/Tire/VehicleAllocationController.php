<?php
// app/Http/Controllers/Tire/VehicleAllocationController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\Vehicle;
use App\Models\TireAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VehicleAllocationController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['currentTires', 'tireAllocations' => function($q) {
            $q->whereNull('removal_date');
        }])->paginate(10);
        
        return view('tire.vehicles.index', compact('vehicles'));
    }

    public function createVehicle()
    {
        return view('tire.vehicles.create');
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

    return redirect()->route('tire.vehicles.index')
        ->with('success', 'Vehicle added successfully!');
}

    public function editVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        return view('tire.vehicles.edit', compact('vehicle'));
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

            return redirect()->route('tire.vehicles.show', $vehicleId)
                ->with('success', 'Vehicle updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update vehicle: ' . $e->getMessage());
        }
    }

    public function deleteVehicle($vehicleId)
    {
        try {
            $vehicle = Vehicle::findOrFail($vehicleId);
            
            // Check if vehicle has any tires allocated
            $hasAllocatedTires = TireAllocation::where('vehicle_id', $vehicleId)
                ->whereNull('removal_date')
                ->exists();
            
            if ($hasAllocatedTires) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Cannot delete vehicle with allocated tires. Please remove all tires first.'
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
        $availableTires = Tire::where('status', 'new')
            ->orWhere('status', 'used')
            ->get();
        
        return view('tire.vehicles.allocate', compact('vehicle', 'availableTires'));
    }

public function allocateTires(Request $request, $vehicleId)
    {
        $validator = Validator::make($request->all(), [
            'tire_ids' => 'required|array|min:1',
            'tire_ids.*' => 'exists:tires,id',
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

            foreach ($request->tire_ids as $index => $tireId) {
                $tire = Tire::findOrFail($tireId);
                
                // Check if tire is already allocated
                if ($tire->status == 'in_use') {
                    throw new \Exception('Tire ' . $tire->serial_number . ' is already in use!');
                }
                
                // Create allocation record
                TireAllocation::create([
                    'tire_id' => $tireId,
                    'vehicle_id' => $vehicleId,
                    'mileage_at_installation' => 0,
                    'position' => isset($request->positions[$index]) ? $request->positions[$index] : null,
                    'installation_date' => now()->toDateString(),
                    'remark' => $request->remark
                ]);

                // Update tire status
                $tire->status = 'in_use';
                $tire->current_location = 'vehicle_' . $vehicleId;
                $tire->save();
            }

            DB::commit();

            return redirect()->route('tire.vehicles.show', $vehicleId)
                ->with('success', 'Tire(s) allocated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to allocate tires: ' . $e->getMessage());
        }
    }

    public function removeTire($allocationId)
    {
        $allocation = TireAllocation::with(['tire', 'vehicle'])->findOrFail($allocationId);
        
        return view('tire.vehicles.remove', compact('allocation'));
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

            $allocation = TireAllocation::with('tire')->findOrFail($allocationId);
            
            // Update allocation with removal details
            $allocation->mileage_at_removal = 0;
            $allocation->removal_date = now()->toDateString();
            $allocation->removal_reason = $request->removal_reason;
            $allocation->save();

            // Update tire status based on action
            $tire = $allocation->tire;
            
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

            return redirect()->route('tire.vehicles.show', $allocation->vehicle_id)
                ->with('success', 'Tire removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove tire: ' . $e->getMessage());
        }
    }

    public function showVehicle($vehicleId)
    {
        $vehicle = Vehicle::with(['tireAllocations' => function($q) {
            $q->with(['tire']);
        }])->findOrFail($vehicleId);
        
        $currentTires = $vehicle->tireAllocations()->whereNull('removal_date')->get();
        $historyTires = $vehicle->tireAllocations()->whereNotNull('removal_date')->get();
        
        return view('tire.vehicles.show', compact('vehicle', 'currentTires', 'historyTires'));
    }

    public function getAvailableTires()
    {
        $availableTires = Tire::where('status', 'new')
            ->orWhere('status', 'used')
            ->select('id', 'serial_number', 'brand', 'size', 'status')
            ->get();
        
        return response()->json($availableTires);
    }

    public function getCurrentTires($vehicleId)
    {
        $currentTires = TireAllocation::where('vehicle_id', $vehicleId)
            ->whereNull('removal_date')
            ->with('tire')
            ->get();
        
        return response()->json($currentTires);
    }
}
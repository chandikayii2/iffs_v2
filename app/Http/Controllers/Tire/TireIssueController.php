<?php
// app/Http/Controllers/Tire/TireIssueController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\Vehicle;
use App\Models\TireIssueNote;
use App\Models\TireIssueNoteItem;
use App\Models\TireAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TireIssueController extends Controller
{
    public function index()
    {
        $issueNotes = TireIssueNote::with(['items.tire', 'items.vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('tire.issue.index', compact('issueNotes'));
    }

    public function create()
    {
        // Generate issue note number
        $lastIssue = TireIssueNote::latest()->first();
        $lastNumber = $lastIssue ? intval(substr($lastIssue->issue_note_number, 4)) : 0;
        $issueNoteNumber = 'IS-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        
        $vehicles = Vehicle::where('status', 'active')->get();
        $tires = Tire::whereIn('status', ['new', 'used'])->get();
        
        return view('tire.issue.create', compact('issueNoteNumber', 'vehicles', 'tires'));
    }

    public function getTireData($tireId)
    {
        try {
            $tire = Tire::findOrFail($tireId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tire->id,
                    'serial_number' => $tire->serial_number,
                    'brand' => $tire->brand,
                    'size' => $tire->size,
                    'type' => $tire->type,
                    'consumption_mileage' => $tire->consumption_mileage,
                    'status' => $tire->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tire not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issue_note_number' => 'required|unique:tire_issue_notes,issue_note_number',
            'issue_date' => 'required|date',
            'tire_ids' => 'required|array|min:1',
            'tire_ids.*' => 'exists:tires,id',
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'consumed_mileages' => 'required|array',
            'consumed_mileages.*' => 'integer|min:0',
            'remarks' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create issue note
            $issueNote = TireIssueNote::create([
                'issue_note_number' => $request->issue_note_number,
                'issue_date' => $request->issue_date,
                'remarks' => null,
                'status' => 'active'
            ]);

            // Add items - each tire can go to different vehicle
            foreach ($request->tire_ids as $index => $tireId) {
                $vehicleId = isset($request->vehicle_ids[$index]) ? $request->vehicle_ids[$index] : null;
                $mileage = isset($request->consumed_mileages[$index]) ? $request->consumed_mileages[$index] : 0;
                $remark = isset($request->remarks[$index]) ? $request->remarks[$index] : null;

                // Create TireIssueNoteItem
                TireIssueNoteItem::create([
                    'tire_issue_note_id' => $issueNote->id,
                    'tire_id' => $tireId,
                    'vehicle_id' => $vehicleId,
                    'consumed_mileage' => $mileage,
                    'remark' => $remark
                ]);

                // ALSO CREATE TIRE ALLOCATION RECORD
                TireAllocation::create([
                    'tire_id' => $tireId,
                    'vehicle_id' => $vehicleId,
                    'mileage_at_installation' => 0,
                    'mileage_at_removal' => null,
                    'position' => null,
                    'installation_date' => $request->issue_date,
                    'removal_date' => null,
                    'removal_reason' => null,
                    'remark' => $remark,
                    'consumed_mileage' => 0
                ]);

                // Update tire status to 'in_use'
                $tire = Tire::find($tireId);
                if ($tire) {
                    $tire->status = 'in_use';
                    $tire->current_location = $vehicleId ? 'vehicle_' . $vehicleId : 'issued';
                    $tire->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tire(s) issued successfully!',
                'data' => $issueNote
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to issue tires: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $issueNote = TireIssueNote::with(['items.tire', 'items.vehicle'])
            ->findOrFail($id);
        
        return view('tire.issue.show', compact('issueNote'));
    }

public function edit($id)
{
    $issueNote = TireIssueNote::with(['items.tire', 'items.vehicle'])->findOrFail($id);
    $vehicles = Vehicle::where('status', 'active')->get();
    
    // Get currently selected tire IDs from the issue note
    $selectedTireIds = $issueNote->items->pluck('tire_id')->toArray();
    
    // Get available tires: new, used, and also the currently selected ones (even if in_use)
    $tires = Tire::whereIn('status', ['new', 'used'])
        ->orWhereIn('id', $selectedTireIds)
        ->get();
    
    return view('tire.issue.edit', compact('issueNote', 'vehicles', 'tires'));
}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'issue_date' => 'required|date',
            'tire_ids' => 'required|array|min:1',
            'tire_ids.*' => 'exists:tires,id',
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'consumed_mileages' => 'required|array',
            'consumed_mileages.*' => 'integer|min:0',
            'remarks' => 'nullable|array',
            'item_ids' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $issueNote = TireIssueNote::findOrFail($id);
            
            // Update issue note
            $issueNote->update([
                'issue_date' => $request->issue_date,
                'remarks' => null
            ]);

            // Get existing item IDs
            $existingItemIds = TireIssueNoteItem::where('tire_issue_note_id', $id)
                ->pluck('id')
                ->toArray();

            $updatedItemIds = $request->item_ids ?? [];

            // Delete removed items and their allocations
            $removedItemIds = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($removedItemIds)) {
                $removedItems = TireIssueNoteItem::whereIn('id', $removedItemIds)->get();
                foreach ($removedItems as $item) {
                    // Delete TireAllocation for this tire
                    TireAllocation::where('tire_id', $item->tire_id)
                        ->whereNull('removal_date')
                        ->delete();
                    $item->delete();
                }
            }

            // Update or create items
            foreach ($request->tire_ids as $index => $tireId) {
                $vehicleId = isset($request->vehicle_ids[$index]) ? $request->vehicle_ids[$index] : null;
                $mileage = isset($request->consumed_mileages[$index]) ? $request->consumed_mileages[$index] : 0;
                $remark = isset($request->remarks[$index]) ? $request->remarks[$index] : null;
                $itemId = isset($request->item_ids[$index]) ? $request->item_ids[$index] : null;

                $item = TireIssueNoteItem::updateOrCreate(
                    ['id' => $itemId],
                    [
                        'tire_issue_note_id' => $id,
                        'tire_id' => $tireId,
                        'vehicle_id' => $vehicleId,
                        'consumed_mileage' => $mileage,
                        'remark' => $remark
                    ]
                );

                // Update or create TireAllocation
                $allocation = TireAllocation::where('tire_id', $tireId)
                    ->whereNull('removal_date')
                    ->first();

                if ($allocation) {
                    // Update existing allocation
                    $allocation->update([
                        'vehicle_id' => $vehicleId,
                        'installation_date' => $request->issue_date,
                        'remark' => $remark
                    ]);
                } else {
                    // Create new allocation
                    TireAllocation::create([
                        'tire_id' => $tireId,
                        'vehicle_id' => $vehicleId,
                        'mileage_at_installation' => 0,
                        'mileage_at_removal' => null,
                        'position' => null,
                        'installation_date' => $request->issue_date,
                        'removal_date' => null,
                        'removal_reason' => null,
                        'remark' => $remark,
                        'consumed_mileage' => 0
                    ]);
                }

                // Update tire status
                $tire = Tire::find($tireId);
                if ($tire) {
                    $tire->status = 'in_use';
                    $tire->current_location = $vehicleId ? 'vehicle_' . $vehicleId : 'issued';
                    $tire->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Issue note updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update issue note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $issueNote = TireIssueNote::findOrFail($id);
            
            // Get all tire IDs from items
            $tireIds = TireIssueNoteItem::where('tire_issue_note_id', $id)
                ->pluck('tire_id')
                ->toArray();
            
            // Delete allocations for these tires
            TireAllocation::whereIn('tire_id', $tireIds)
                ->whereNull('removal_date')
                ->delete();
            
            // Update tire status back to 'used'
            foreach ($tireIds as $tireId) {
                $tire = Tire::find($tireId);
                if ($tire) {
                    $tire->status = 'used';
                    $tire->current_location = 'store';
                    $tire->save();
                }
            }
            
            // Delete items
            TireIssueNoteItem::where('tire_issue_note_id', $id)->delete();
            
            // Delete issue note
            $issueNote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Issue note deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete issue note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePdf($id)
    {
        $issueNote = TireIssueNote::with(['items.tire', 'items.vehicle'])
            ->findOrFail($id);
        
        return view('tire.issue.pdf', compact('issueNote'));
    }

    public function generateGatePass($id)
    {
        $issueNote = TireIssueNote::with(['items.tire', 'items.vehicle'])
            ->findOrFail($id);
        
        $gatePassNumber = 'GP-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        
        return view('tire.issue.gate_pass', compact('issueNote', 'gatePassNumber'));
    }
}
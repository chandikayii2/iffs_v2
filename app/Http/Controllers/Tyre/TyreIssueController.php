<?php
// app/Http/Controllers/Tyre/TyreIssueController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\Vehicle;
use App\Models\TyreIssueNote;
use App\Models\TyreIssueNoteItem;
use App\Models\TyreAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TyreIssueController extends Controller
{
    public function index()
    {
        $issueNotes = TyreIssueNote::with(['items.tyre', 'items.vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('tyre.issue.index', compact('issueNotes'));
    }

    public function create()
    {
        // Generate issue note number
        $lastIssue = TyreIssueNote::latest()->first();
        $lastNumber = $lastIssue ? intval(substr($lastIssue->issue_note_number, 4)) : 0;
        $issueNoteNumber = 'IS-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        
        $vehicles = Vehicle::where('status', 'active')->get();
        $tyres = Tyre::whereIn('status', ['new', 'used'])->get();
        
        return view('tyre.issue.create', compact('issueNoteNumber', 'vehicles', 'tyres'));
    }

    public function getTyreData($tyreId)
    {
        try {
            $tyre = Tyre::findOrFail($tyreId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tyre->id,
                    'serial_number' => $tyre->serial_number,
                    'brand' => $tyre->brand,
                    'size' => $tyre->size,
                    'type' => $tyre->type,
                    'consumption_mileage' => $tyre->consumption_mileage,
                    'status' => $tyre->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tyre not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issue_note_number' => 'required|unique:tire_issue_notes,issue_note_number',
            'issue_date' => 'required|date',
            'tyre_ids' => 'required|array|min:1',
            'tyre_ids.*' => 'exists:tires,id',
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
            $issueNote = TyreIssueNote::create([
                'issue_note_number' => $request->issue_note_number,
                'issue_date' => $request->issue_date,
                'remarks' => null,
                'status' => 'active'
            ]);

            // Add items - each tyre can go to different vehicle
            foreach ($request->tyre_ids as $index => $tyreId) {
                $vehicleId = isset($request->vehicle_ids[$index]) ? $request->vehicle_ids[$index] : null;
                $mileage = isset($request->consumed_mileages[$index]) ? $request->consumed_mileages[$index] : 0;
                $remark = isset($request->remarks[$index]) ? $request->remarks[$index] : null;

                // Create TyreIssueNoteItem
                TyreIssueNoteItem::create([
                    'tire_issue_note_id' => $issueNote->id,
                    'tire_id' => $tyreId,
                    'vehicle_id' => $vehicleId,
                    'consumed_mileage' => $mileage,
                    'remark' => $remark
                ]);

                // ALSO CREATE TIRE ALLOCATION RECORD
                TyreAllocation::create([
                    'tire_id' => $tyreId,
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

                // Update tyre status to 'in_use'
                $tyre = Tyre::find($tyreId);
                if ($tyre) {
                    $tyre->status = 'in_use';
                    $tyre->current_location = $vehicleId ? 'vehicle_' . $vehicleId : 'issued';
                    $tyre->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tyre(s) issued successfully!',
                'data' => $issueNote
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to issue tyres: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $issueNote = TyreIssueNote::with(['items.tyre', 'items.vehicle'])
            ->findOrFail($id);
        
        return view('tyre.issue.show', compact('issueNote'));
    }

public function edit($id)
{
    $issueNote = TyreIssueNote::with(['items.tyre', 'items.vehicle'])->findOrFail($id);
    $vehicles = Vehicle::where('status', 'active')->get();
    
    // Get currently selected tyre IDs from the issue note
    $selectedTyreIds = $issueNote->items->pluck('tire_id')->toArray();
    
    // Get available tyres: new, used, and also the currently selected ones (even if in_use)
    $tyres = Tyre::whereIn('status', ['new', 'used'])
        ->orWhereIn('id', $selectedTyreIds)
        ->get();
    
    return view('tyre.issue.edit', compact('issueNote', 'vehicles', 'tyres'));
}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'issue_date' => 'required|date',
            'tyre_ids' => 'required|array|min:1',
            'tyre_ids.*' => 'exists:tires,id',
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

            $issueNote = TyreIssueNote::findOrFail($id);
            
            // Update issue note
            $issueNote->update([
                'issue_date' => $request->issue_date,
                'remarks' => null
            ]);

            // Get existing item IDs
            $existingItemIds = TyreIssueNoteItem::where('tire_issue_note_id', $id)
                ->pluck('id')
                ->toArray();

            $updatedItemIds = $request->item_ids ?? [];

            // Delete removed items and their allocations
            $removedItemIds = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($removedItemIds)) {
                $removedItems = TyreIssueNoteItem::whereIn('id', $removedItemIds)->get();
                foreach ($removedItems as $item) {
                    // Delete TyreAllocation for this tyre
                    TyreAllocation::where('tire_id', $item->tyre_id)
                        ->whereNull('removal_date')
                        ->delete();
                    $item->delete();
                }
            }

            // Update or create items
            foreach ($request->tyre_ids as $index => $tyreId) {
                $vehicleId = isset($request->vehicle_ids[$index]) ? $request->vehicle_ids[$index] : null;
                $mileage = isset($request->consumed_mileages[$index]) ? $request->consumed_mileages[$index] : 0;
                $remark = isset($request->remarks[$index]) ? $request->remarks[$index] : null;
                $itemId = isset($request->item_ids[$index]) ? $request->item_ids[$index] : null;

                $item = TyreIssueNoteItem::updateOrCreate(
                    ['id' => $itemId],
                    [
                        'tire_issue_note_id' => $id,
                        'tire_id' => $tyreId,
                        'vehicle_id' => $vehicleId,
                        'consumed_mileage' => $mileage,
                        'remark' => $remark
                    ]
                );

                // Update or create TyreAllocation
                $allocation = TyreAllocation::where('tire_id', $tyreId)
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
                    TyreAllocation::create([
                        'tire_id' => $tyreId,
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

                // Update tyre status
                $tyre = Tyre::find($tyreId);
                if ($tyre) {
                    $tyre->status = 'in_use';
                    $tyre->current_location = $vehicleId ? 'vehicle_' . $vehicleId : 'issued';
                    $tyre->save();
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
            $issueNote = TyreIssueNote::findOrFail($id);
            
            // Get all tyre IDs from items
            $tyreIds = TyreIssueNoteItem::where('tire_issue_note_id', $id)
                ->pluck('tire_id')
                ->toArray();
            
            // Delete allocations for these tyres
            TyreAllocation::whereIn('tire_id', $tyreIds)
                ->whereNull('removal_date')
                ->delete();
            
            // Update tyre status back to 'used'
            foreach ($tyreIds as $tyreId) {
                $tyre = Tyre::find($tyreId);
                if ($tyre) {
                    $tyre->status = 'used';
                    $tyre->current_location = 'store';
                    $tyre->save();
                }
            }
            
            // Delete items
            TyreIssueNoteItem::where('tire_issue_note_id', $id)->delete();
            
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
        $issueNote = TyreIssueNote::with(['items.tyre', 'items.vehicle'])
            ->findOrFail($id);
        
        return view('tyre.issue.pdf', compact('issueNote'));
    }

    public function generateGatePass($id)
    {
        $issueNote = TyreIssueNote::with(['items.tyre', 'items.vehicle'])
            ->findOrFail($id);
        
        $gatePassNumber = 'GP-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        
        return view('tyre.issue.gate_pass', compact('issueNote', 'gatePassNumber'));
    }
}
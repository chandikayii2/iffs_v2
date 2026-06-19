<?php
// app/Http/Controllers/Tire/RefillingController.php
namespace App\Http\Controllers\Tire;

use App\Http\Controllers\Controller;
use App\Models\Tire;
use App\Models\RefillingVendor;
use App\Models\RefillingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RefillingController extends Controller
{
    public function index()
    {
        $orders = RefillingOrder::with(['vendor', 'tires'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tire.refilling.index', compact('orders'));
    }
    
    public function VendorsManage()
    {
        $vendors = RefillingVendor::withCount('refillingOrders')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('tire.refilling.vendors', compact('vendors'));
    }

    public function createOrder(Request $request)
    {
        $vendors = RefillingVendor::all();
        $availableTires = Tire::whereIn('status', ['used', 'new'])
            ->where('status', '!=', 'in_use')
            ->where('status', '!=', 'scrap')
            ->get();

        // Generate order number
        $lastOrder = RefillingOrder::latest()->first();
        $lastNumber = $lastOrder ? intval(substr($lastOrder->order_number, 4)) : 0;
        $orderNumber = 'REF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

        $selectedTireId = $request->get('tire_id');

        return view('tire.refilling.create', compact('vendors', 'availableTires', 'orderNumber', 'selectedTireId'));
    }

    public function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required|unique:refilling_orders,order_number',
            'vendor_id' => 'required|exists:refilling_vendors,id',
            'sent_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:sent_date',
            'tire_ids' => 'required|array|min:1',
            'tire_ids.*' => 'exists:tires,id',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $order = RefillingOrder::create([
                'order_number' => $request->order_number,
                'vendor_id' => $request->vendor_id,
                'sent_date' => $request->sent_date,
                'expected_return_date' => $request->expected_return_date,
                'status' => 'sent',
                'notes' => $request->notes
            ]);

            foreach ($request->tire_ids as $tireId) {
                $order->tires()->attach($tireId, [
                    'refilling_cost' => null,
                    'notes' => null
                ]);
                
                $tire = Tire::find($tireId);
                if ($tire) {
                    $tire->status = 'at_vendor';
                    $tire->current_location = 'vendor_' . $request->vendor_id;
                    $tire->save();
                }
            }

            DB::commit();

            return redirect()->route('tire.refilling.show', $order->id)
                ->with('success', 'Refilling order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function showOrder($orderId)
    {
        $order = RefillingOrder::with(['vendor', 'tires', 'tires.scrapRecord'])
            ->findOrFail($orderId);
        
        return view('tire.refilling.show', compact('order'));
    }

    public function receiveOrder($orderId)
    {
        $order = RefillingOrder::with(['tires', 'vendor'])->findOrFail($orderId);
        
        return view('tire.refilling.receive', compact('order'));
    }

    public function processReceipt(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'received_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'refilling_costs' => 'required|array',
            'refilling_costs.*' => 'numeric|min:0',
            'refill_counts' => 'required|array',
            'refill_counts.*' => 'integer|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $order = RefillingOrder::findOrFail($orderId);
            
            $order->received_date = $request->received_date;
            $order->total_cost = $request->total_cost;
            $order->status = 'received';
            $order->save();

            foreach ($order->tires as $index => $tire) {
                $order->tires()->updateExistingPivot($tire->id, [
                    'refilling_cost' => $request->refilling_costs[$index] ?? 0
                ]);
                
                $tire->refill_count += $request->refill_counts[$index];
                $tire->status = 'used';
                $tire->current_location = 'store';
                $tire->save();
            }

            DB::commit();

            return redirect()->route('tire.refilling.index')
                ->with('success', 'Order received and updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process receipt: ' . $e->getMessage());
        }
    }

    // ============================================
    // VENDOR MANAGEMENT METHODS
    // ============================================
    
    public function manageVendors()
    {
        $vendors = RefillingVendor::withCount('refillingOrders')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('tire.refilling.vendors', compact('vendors'));
    }
    
    public function showVendor($vendorId)
    {
        $vendor = RefillingVendor::withCount('refillingOrders')
            ->with('refillingOrders')
            ->findOrFail($vendorId);
        
        return view('tire.refilling.vendor_details', compact('vendor'));
    }
    
    public function storeVendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:refilling_vendors,name',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $vendor = RefillingVendor::create($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor added successfully!',
                    'vendor_id' => $vendor->id,
                    'vendor_display' => $vendor->name . ' - ' . $vendor->contact_person . ' (' . $vendor->phone . ')'
                ]);
            }

            return redirect()->route('tire.refilling.vendors')
                ->with('success', 'Vendor added successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add vendor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to add vendor: ' . $e->getMessage());
        }
    }
    
    public function editVendor($vendorId)
    {
        $vendor = RefillingVendor::findOrFail($vendorId);
        
        if (request()->ajax()) {
            return response()->json($vendor);
        }
        
        return view('tire.refilling.vendors_edit', compact('vendor'));
    }
    
    public function updateVendor(Request $request, $vendorId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:refilling_vendors,name,' . $vendorId,
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $vendor = RefillingVendor::findOrFail($vendorId);
            $vendor->update($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor updated successfully!'
                ]);
            }

            return redirect()->route('tire.refilling.vendors')
                ->with('success', 'Vendor updated successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update vendor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }
    
    public function deleteVendor($vendorId)
    {
        try {
            $vendor = RefillingVendor::findOrFail($vendorId);
            
            if ($vendor->refillingOrders()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete vendor with existing orders!'
                ], 400);
            }
            
            $vendor->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vendor: ' . $e->getMessage()
            ], 500);
        }
    }
}
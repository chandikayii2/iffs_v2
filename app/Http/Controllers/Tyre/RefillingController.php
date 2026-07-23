<?php
// app/Http/Controllers/Tyre/RefillingController.php
namespace App\Http\Controllers\Tyre;

use App\Http\Controllers\Controller;
use App\Models\Tyre;
use App\Models\RefillingVendor;
use App\Models\RefillingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RefillingController extends Controller
{
    public function index(Request $request)
    {
        $query = RefillingOrder::with(['vendor', 'tyres']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhereHas('vendor', function($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'LIKE', "%{$search}%")
                                  ->orWhere('contact_person', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('tyres', function($tyreQuery) use ($search) {
                      $tyreQuery->where('serial_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Payment status filter
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $orders->appends([
            'search' => $request->search,
            'payment_status' => $request->payment_status
        ]);

        return view('tyre.refilling.index', compact('orders'));
    }
    
    public function VendorsManage()
    {
        $vendors = RefillingVendor::withCount('refillingOrders')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('tyre.refilling.vendors', compact('vendors'));
    }

    public function createOrder(Request $request)
    {
        $vendors = RefillingVendor::all();
        $availableTyres = Tyre::where(function($q) {
            $q->whereIn('status', ['used', 'new'])
              ->orWhere(function($subQ) {
                  $subQ->where('status', 'at_vendor')
                       ->whereIn('current_location', ['store', 'pending_refill']);
              });
        })
        ->where('status', '!=', 'in_use')
        ->where('status', '!=', 'scrap')
        ->get();

        // Generate order number
        $lastOrder = RefillingOrder::latest()->first();
        $lastNumber = $lastOrder ? intval(substr($lastOrder->order_number, 4)) : 0;
        $orderNumber = 'REF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

        $selectedTyreId = $request->get('tire_id');

        return view('tyre.refilling.create', compact('vendors', 'availableTyres', 'orderNumber', 'selectedTyreId'));
    }

    public function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required|unique:refilling_orders,order_number',
            'vendor_id' => 'required|exists:refilling_vendors,id',
            'sent_date' => 'required|date',
            'tyre_ids' => 'required|array|min:1',
            'tyre_ids.*' => 'exists:tires,id',
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
                'status' => 'sent',
                'notes' => $request->notes
            ]);

            foreach ($request->tyre_ids as $tyreId) {
                $order->tyres()->attach($tyreId, [
                    'refilling_cost' => null,
                    'notes' => null
                ]);
                
                $tyre = Tyre::find($tyreId);
                if ($tyre) {
                    $tyre->status = 'at_vendor';
                    $tyre->current_location = 'vendor_' . $request->vendor_id;
                    $tyre->save();
                }
            }

            DB::commit();

            return redirect()->route('tyre.refilling.show', $order->id)
                ->with('success', 'Refilling order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function showOrder($orderId)
    {
        $order = RefillingOrder::with(['vendor', 'tyres', 'tyres.scrapRecord'])
            ->findOrFail($orderId);
        
        return view('tyre.refilling.show', compact('order'));
    }

    public function receiveOrder($orderId)
    {
        $order = RefillingOrder::with(['tyres', 'vendor'])->findOrFail($orderId);
        
        return view('tyre.refilling.receive', compact('order'));
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

            foreach ($order->tyres as $index => $tyre) {
                $order->tyres()->updateExistingPivot($tyre->id, [
                    'refilling_cost' => $request->refilling_costs[$index] ?? 0
                ]);
                
                $tyre->refill_count += $request->refill_counts[$index];
                $tyre->status = 'used';
                $tyre->current_location = 'store';
                $tyre->save();
            }

            DB::commit();

            return redirect()->route('tyre.refilling.index')
                ->with('success', 'Order received and updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process receipt: ' . $e->getMessage());
        }
    }

    public function recordPayment(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'payment_date' => 'required|date',
            'payment_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order = RefillingOrder::findOrFail($orderId);
            
            $totalCost = (float) $order->total_cost;
            $paidAmount = (float) $request->paid_amount;
            
            if ($paidAmount >= $totalCost) {
                $order->payment_status = 'paid';
            } elseif ($paidAmount > 0 && $paidAmount < $totalCost) {
                $order->payment_status = 'partial';
            } else {
                $order->payment_status = 'unpaid';
            }
            
            $order->paid_amount = $paidAmount;
            $order->payment_method = $request->payment_method;
            $order->payment_reference = $request->payment_reference;
            $order->payment_date = $request->payment_date;
            $order->payment_notes = $request->payment_notes;
            $order->save();

            return redirect()->route('tyre.refilling.index')
                ->with('success', 'Payment recorded successfully for order ' . $order->order_number);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for Refilling Order
     */
    public function generatePdf($orderId)
    {
        $order = RefillingOrder::with(['vendor', 'tyres'])
            ->findOrFail($orderId);
        
        return view('tyre.refilling.pdf', compact('order'));
    }

    // ============================================
    // VENDOR MANAGEMENT METHODS
    // ============================================
    
    public function manageVendors()
    {
        $vendors = RefillingVendor::withCount('refillingOrders')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('tyre.refilling.vendors', compact('vendors'));
    }
    
    public function showVendor($vendorId)
    {
        $vendor = RefillingVendor::withCount('refillingOrders')
            ->with('refillingOrders')
            ->findOrFail($vendorId);
        
        return view('tyre.refilling.vendor_details', compact('vendor'));
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

            return redirect()->route('tyre.refilling.vendors')
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
        
        return view('tyre.refilling.vendors_edit', compact('vendor'));
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

            return redirect()->route('tyre.refilling.vendors')
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
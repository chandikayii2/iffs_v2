<?php
// app/Http/Controllers/V1/Auth/AuthController.php (Updated)

namespace App\Http\Controllers\V1\Auth;

use Exception;
use App\Models\Grn;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\IssueNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function dashboardView()
    {
        // Get the count of purchase orders
        $purchaseOrderCount = PurchaseOrder::count();

        // Get the count of GRNs
        $grnCount = Grn::count();

        // Get the count of stocks
        $stockCount = Product::count();

        // Get the count of issue notes
        $issueNoteCount = IssueNote::count();

        // Get user permissions
        $getLoginUserPermission = session('user_permissions', []);

        // Pass the counts to the view
        return view('dashboard', [
            'purchaseOrderCount' => $purchaseOrderCount,
            'grnCount' => $grnCount,
            'stockCount' => $stockCount,
            'issueNoteCount' => $issueNoteCount,
            'getLoginUserPermission' => $getLoginUserPermission,
        ]);
    }

    public function loginView()
    {
        return view('Auth.login');
    }

    public function loginCheck(Request $attributes)
    {
        $validator = Validator::make($attributes->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withErrors(['message' => $validator->errors()->first()]);
        }

        $response = $this->authService->loginCheck($attributes);

        if ($response['status'] === 200) {
            // Store user permissions in session
            if (isset($response['data']['permissions'])) {
                session(['user_permissions' => $response['data']['permissions']]);
            }
            
            // Redirect to welcome page instead of dashboard directly
            return redirect()->route('welcome')->with(['message' => $response['message'], 'data' => $response['data'], 'status' => $response['status']]);
        } else {
            return redirect()->route('login')->withErrors(['message' => $response['message']]);
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->flush(); // Clear all session data
        return redirect('/login');
    }
}
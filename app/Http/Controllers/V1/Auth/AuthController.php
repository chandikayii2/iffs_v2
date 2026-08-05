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

use Illuminate\Support\Facades\DB;

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
        $getLoginUserPermission = session('user_permissions');
        if (empty($getLoginUserPermission) && Auth::check()) {
            $getLoginUserPermission = DB::table('role_permissions')
                ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->select('role_permissions.*', 'roles.role_name', 'permissions.name', 'permissions.slug')
                ->where('role_permissions.role_id', Auth::user()->role_id)
                ->get();
            session(['user_permissions' => $getLoginUserPermission]);
        }

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
            $user = $response['data'];
            
            // Store user permissions in session by querying DB
            if ($user && isset($user->role_id)) {
                $permissions = DB::table('role_permissions')
                    ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                    ->select('role_permissions.*', 'roles.role_name', 'permissions.name', 'permissions.slug')
                    ->where('role_permissions.role_id', $user->role_id)
                    ->get();
                session(['user_permissions' => $permissions]);
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
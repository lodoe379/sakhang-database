<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\FurnitureLog;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // Re-check authentication if needed or use standard Laravel auth middleware
        if (!Session::get('loggedin')) {
            return redirect()->route('landing');
        }

        $complaints = Complaint::latest()->get();
        $furniture = FurnitureLog::latest()->get();

        $lendCount = $furniture->where('type', 'Lend')->count();
        $returnCount = $furniture->where('type', 'Return')->count();

        $currentLend = 0;
        $currentReturn = 0;

        $furniture = $furniture->map(function ($item) use (&$currentLend, &$currentReturn) {
            if ($item->type === 'Lend') {
                $item->s_no = ++$currentLend;
            } else {
                $item->s_no = ++$currentReturn;
            }
            return $item;
        });

        return view('dashboard', [
            'complaints' => $complaints,
            'furniture' => $furniture,
            'total_complaints' => $complaints->count(),
            'done_complaints' => $complaints->where('done', true)->count(),
            'incomplete_complaints' => $complaints->count() - $complaints->where('done', true)->count(),
            'total_lend' => $lendCount,
            'lend_done' => $furniture->where('type', 'Lend')->where('done', true)->count(),
            'lend_pending' => $lendCount - $furniture->where('type', 'Lend')->where('done', true)->count(),
            'total_return' => $returnCount,
            'return_done' => $furniture->where('type', 'Return')->where('done', true)->count(),
            'return_pending' => $returnCount - $furniture->where('type', 'Return')->where('done', true)->count(),
        ]);
    }

    public function furnitureManagement(): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $is_loggedin = Session::get('loggedin');
        
        if ($is_loggedin && Session::get('role') !== 'admin' && Session::get('role') !== 'staff') {
            return redirect()->route('dashboard');
        }

        $nextLendSno = FurnitureLog::where('type', 'Lend')->count() + 1;
        $nextReturnSno = FurnitureLog::where('type', 'Return')->count() + 1;
        $furniture = FurnitureLog::latest()->get();

        $total_lend = $furniture->where('type', 'Lend')->count();
        $lend_done = $furniture->where('type', 'Lend')->where('done', true)->count();
        $lend_pending = $total_lend - $lend_done;

        $total_return = $furniture->where('type', 'Return')->count();
        $return_done = $furniture->where('type', 'Return')->where('done', true)->count();
        $return_pending = $total_return - $return_done;

        return view('furniture_management', [
            'furniture' => $furniture,
            'nextLendSno' => $nextLendSno,
            'nextReturnSno' => $nextReturnSno,
            'total_lend' => $total_lend,
            'lend_done' => $lend_done,
            'lend_pending' => $lend_pending,
            'total_return' => $total_return,
            'return_done' => $return_done,
            'return_pending' => $return_pending,
            'is_loggedin' => $is_loggedin
        ]);
    }


    public function login(Request $request)
    {
        // Define valid users and their passwords
        $valid_users = [
            'admin' => '123',   // Main Admin
            'staff' => '123',   // Staff Access
            'staff1' => '123'   // New Staff Access with extra permissions
        ];

        if (array_key_exists($request->id, $valid_users) && $valid_users[$request->id] === $request->password) {
            Session::put('loggedin', true);
            Session::put('role', $request->id); // 'admin' or 'staff'
            return redirect()->route('dashboard');
        }

        return redirect()->route('landing')->with(['error' => 'Access Denied: Invalid Credentials', 'active_mode' => 'admin']);
    }

    public function logout()
    {
        Session::forget('loggedin');
        return redirect()->route('landing');
    }
}

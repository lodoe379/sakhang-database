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

        $emptyRooms = \App\Models\EmptyRoom::all();
        $roomData = [];
        foreach($emptyRooms as $r) {
            $roomData[] = [
                'building' => $r->building,
                'room' => $r->room,
                'bed' => $r->bed,
                'table' => $r->table,
                'chair' => $r->chair,
                'cupboard' => $r->cupboard,
                'name_on_bill' => $r->name_on_bill,
                'in_id' => $r->in_id,
                'meter_number' => $r->meter_number,
                'consumer_id' => $r->consumer_id,
                'account_no' => $r->account_no,
                'meter_image' => $r->meter_image,
            ];
        }

        // Filter for ONLY empty rooms (rooms with no name_on_bill)
        $emptyRoomData = [];
        foreach ($roomData as $key => $room) {
            if (empty($room['name_on_bill'])) {
                $emptyRoomData[] = $room;
            }
        }
        $roomData = array_values($emptyRoomData);

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
            'roomData' => $roomData,
            'total_rooms' => count($roomData),
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

    public function updateRoom(Request $request)
    {
        $building = $request->input('building');
        $room = $request->input('room');
        $field = $request->input('field');
        $value = $request->input('value');

        $er = \App\Models\EmptyRoom::where('building', $building)->where('room', $room)->first();
        if ($er) {
            $er->$field = $value;
            $er->save();
        } else {
            \App\Models\EmptyRoom::create([
                'building' => $building,
                'room' => $room,
                $field => $value
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function saveRoomRow(Request $request)
    {
        if (!Session::get('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $building = $request->input('building');
        $room = $request->input('room');

        if (!$building || !$room) {
            return response()->json(['error' => 'Building and Room are required'], 400);
        }

        $er = \App\Models\EmptyRoom::where('building', $building)->where('room', $room)->first();
        if (!$er) {
            $er = new \App\Models\EmptyRoom();
            $er->building = $building;
            $er->room = $room;
        }
        
        // Update all fields
        $er->bed = $request->input('bed', '');
        $er->table = $request->input('table', '');
        $er->chair = $request->input('chair', '');
        $er->cupboard = $request->input('cupboard', '');
        $er->name_on_bill = $request->input('name_on_bill', '');
        $er->in_id = $request->input('in_id', '');
        $er->meter_number = $request->input('meter_number', '');
        $er->consumer_id = $request->input('consumer_id', '');
        $er->account_no = $request->input('account_no', '');
        
        $er->save();

        return response()->json(['success' => true]);
    }

    public function deleteRoom(Request $request)
    {
        if (!Session::get('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $building = $request->input('building');
        $room = $request->input('room');

        if (!$building || !$room) {
            return response()->json(['error' => 'Building and Room are required'], 400);
        }

        \App\Models\EmptyRoom::where('building', $building)->where('room', $room)->delete();

        return response()->json(['success' => true]);
    }

    public function searchRoomData(Request $request)
    {
        if (!Session::get('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $building = $request->input('building');
        $room = $request->input('room');

        if (!$building || !$room) {
            return response()->json(['error' => 'Building and Room are required'], 400);
        }

        // Fetch from EmptyRoom first
        $er = \App\Models\EmptyRoom::where('building', $building)
            ->where('room', $room)
            ->first();

        // Fetch from RoomFurniture as fallback
        $rf = \App\Models\RoomFurniture::where('building', $building)
            ->where('room', $room)
            ->first();

        // Fetch from MeterReading as fallback
        $mr = \App\Models\MeterReading::where('building', $building)
            ->where('room', $room)
            ->first();

        $data = [
            'building' => $building,
            'room' => $room,
            'bed' => $er ? $er->bed : ($rf ? $rf->bed : ''),
            'table' => $er ? $er->table : ($rf ? $rf->table : ''),
            'chair' => $er ? $er->chair : ($rf ? $rf->chair : ''),
            'cupboard' => $er ? $er->cupboard : ($rf ? $rf->cupboard : ''),
            'name_on_bill' => $er ? $er->name_on_bill : ($mr ? $mr->name_on_bill : ''),
            'in_id' => $er ? $er->in_id : ($mr ? $mr->in_id : ''),
            'meter_number' => $er ? $er->meter_number : ($mr ? $mr->meter_number : ''),
            'consumer_id' => $er ? $er->consumer_id : ($mr ? $mr->consumer_id : ''),
            'account_no' => $er ? $er->account_no : ($mr ? $mr->account_no : ''),
            'meter_image' => $er ? $er->meter_image : ($mr ? $mr->meter_image : '')
        ];

        return response()->json($data);
    }
}

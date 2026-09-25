<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\FurnitureLog;
use App\Models\RoomFurniture;
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

        $meters = \App\Models\MeterReading::all();
        $furnitures = \App\Models\RoomFurniture::all();
        
        $roomsMap = [];
        
        foreach($meters as $m) {
            $key = strtolower(trim($m->building)) . '|||' . strtolower(trim($m->room));
            $roomsMap[$key] = [
                'building' => trim($m->building),
                'room' => trim($m->room),
                'bed' => '',
                'table' => '',
                'chair' => '',
                'cupboard' => '',
                'name_on_bill' => $m->name_on_bill,
                'in_id' => $m->in_id,
                'meter_number' => $m->meter_number,
                'consumer_id' => $m->consumer_id,
                'account_no' => $m->account_no,
                'meter_image' => $m->meter_image,
            ];
        }

        foreach($furnitures as $f) {
            $key = strtolower(trim($f->building)) . '|||' . strtolower(trim($f->room));
            if (!isset($roomsMap[$key])) {
                $roomsMap[$key] = [
                    'building' => trim($f->building),
                    'room' => trim($f->room),
                    'bed' => '',
                    'table' => '',
                    'chair' => '',
                    'cupboard' => '',
                    'name_on_bill' => '',
                    'in_id' => '',
                    'meter_number' => '',
                    'consumer_id' => '',
                    'account_no' => '',
                    'meter_image' => '',
                ];
            }
            $roomsMap[$key]['bed'] = $f->bed;
            $roomsMap[$key]['table'] = $f->table;
            $roomsMap[$key]['chair'] = $f->chair;
            $roomsMap[$key]['cupboard'] = $f->cupboard;
        }

        $roomData = array_values($roomsMap);

        // Filter for ONLY empty rooms (rooms with no name_on_bill)
        $emptyRoomData = [];
        foreach ($roomData as $key => $room) {
            if (empty(trim($room['name_on_bill']))) {
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

        $furnitureFields = ['bed', 'table', 'chair', 'cupboard'];
        $meterFields = ['name_on_bill', 'in_id', 'meter_number', 'consumer_id', 'account_no'];

        if (in_array($field, $furnitureFields)) {
            $rf = \App\Models\RoomFurniture::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                           ->whereRaw('LOWER(room) = ?', [strtolower($room)])->first();
            if ($rf) {
                $rf->$field = $value;
                $rf->save();
            } else {
                \App\Models\RoomFurniture::create([
                    'building' => $building,
                    'room' => $room,
                    $field => $value
                ]);
            }
        } elseif (in_array($field, $meterFields)) {
            $mr = \App\Models\MeterReading::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                          ->whereRaw('LOWER(room) = ?', [strtolower($room)])->first();
            if ($mr) {
                $mr->$field = $value;
                $mr->save();
            } else {
                \App\Models\MeterReading::create([
                    'building' => $building,
                    'room' => $room,
                    $field => $value
                ]);
            }
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

        $rf = \App\Models\RoomFurniture::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                       ->whereRaw('LOWER(room) = ?', [strtolower($room)])->first();
        if (!$rf) {
            $rf = new \App\Models\RoomFurniture();
            $rf->building = $building;
            $rf->room = $room;
        }
        $rf->bed = $request->input('bed', '');
        $rf->table = $request->input('table', '');
        $rf->chair = $request->input('chair', '');
        $rf->cupboard = $request->input('cupboard', '');
        $rf->save();

        $mr = \App\Models\MeterReading::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                      ->whereRaw('LOWER(room) = ?', [strtolower($room)])->first();
        if (!$mr) {
            $mr = new \App\Models\MeterReading();
            $mr->building = $building;
            $mr->room = $room;
        }
        $mr->name_on_bill = $request->input('name_on_bill', '');
        $mr->in_id = $request->input('in_id', '');
        $mr->meter_number = $request->input('meter_number', '');
        $mr->consumer_id = $request->input('consumer_id', '');
        $mr->account_no = $request->input('account_no', '');
        $mr->save();

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

        \App\Models\RoomFurniture::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                 ->whereRaw('LOWER(room) = ?', [strtolower($room)])->delete();
        \App\Models\MeterReading::whereRaw('LOWER(building) = ?', [strtolower($building)])
                                ->whereRaw('LOWER(room) = ?', [strtolower($room)])->delete();

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

        // Fetch from RoomFurniture
        $rf = \App\Models\RoomFurniture::whereRaw('LOWER(building) = ?', [strtolower($building)])
            ->whereRaw('LOWER(room) = ?', [strtolower($room)])
            ->first();

        // Fetch from MeterReading
        $mr = \App\Models\MeterReading::whereRaw('LOWER(building) = ?', [strtolower($building)])
            ->whereRaw('LOWER(room) = ?', [strtolower($room)])
            ->first();

        $data = [
            'building' => $building,
            'room' => $room,
            'bed' => $rf ? $rf->bed : '',
            'table' => $rf ? $rf->table : '',
            'chair' => $rf ? $rf->chair : '',
            'cupboard' => $rf ? $rf->cupboard : '',
            'name_on_bill' => $mr ? $mr->name_on_bill : '',
            'in_id' => $mr ? $mr->in_id : '',
            'meter_number' => $mr ? $mr->meter_number : '',
            'consumer_id' => $mr ? $mr->consumer_id : '',
            'account_no' => $mr ? $mr->account_no : '',
            'meter_image' => $mr ? $mr->meter_image : ''
        ];

        return response()->json($data);
    }
}

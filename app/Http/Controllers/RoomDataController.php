<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\RoomFurniture;
use App\Models\MeterReading;

class RoomDataController extends Controller
{
    public function index()
    {
        if (!Session::get('loggedin')) {
            return redirect()->route('landing');
        }

        // Get all unique building+room combinations from MeterReading
        $meters = MeterReading::all();
        $furnitures = RoomFurniture::all();
        
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

        // Filter for empty rooms (where name_on_bill is empty)
        $emptyRoomData = [];
        foreach ($roomData as $room) {
            if (empty(trim($room['name_on_bill']))) {
                $emptyRoomData[] = $room;
            }
        }

        return view('room_data', [
            'roomData' => $emptyRoomData
        ]);
    }
}

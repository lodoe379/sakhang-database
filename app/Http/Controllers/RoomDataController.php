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

        return view('room_data', [
            'roomData' => $roomData
        ]);
    }
}

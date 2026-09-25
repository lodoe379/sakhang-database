<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsumerController extends Controller
{
    public function search(Request $request)
    {
        if (!$request->has('room')) {
            return redirect()->route('landing')->with('active_mode', 'consumer');
        }

        $building = $request->building;
        $room = $request->room; // This is used for Room No, Name, Dept, etc.

        $json_path = storage_path('app/consumers.json');
        $consumers = [];

        if (file_exists($json_path)) {
            $consumers_db = json_decode(file_get_contents($json_path), true);
            if ($consumers_db) {
                foreach ($consumers_db as $c) {
                    $match_building = true;
                    if ($building) {
                        $match_building = isset($c['building']) && stripos($c['building'], $building) !== false;
                    }

                    $match_room = true;
                    if ($room) {
                        $name_match = isset($c['name']) && stripos($c['name'], $room) !== false;
                        $dept_match = isset($c['department']) && stripos($c['department'], $room) !== false;
                        $build_match = isset($c['building']) && stripos($c['building'], $room) !== false;
                        $r_match = isset($c['room']) && ((string) $c['room'] === (string) $room);
                        
                        $match_room = $name_match || $dept_match || $build_match || $r_match;
                    }

                    if ($match_building && $match_room) {
                        $consumers[] = $c;
                    }
                }
            }
        }

        return view('landing', [
            'consumers' => $consumers,
            'active_mode' => 'consumer',
            'search_building' => $building,
            'search_room' => $room,
            'nextComplaintSno' => \App\Models\Complaint::count() + 1,
            'nextFurnitureSno' => \App\Models\FurnitureLog::count() + 1,
        ]);
    }
}

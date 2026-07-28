<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsumerController extends Controller
{
    public function search(Request $request)
    {
        if (!$request->has('building')) {
            return redirect()->route('landing')->with('active_mode', 'consumer');
        }

        $building = $request->building;
        $room = $request->room; // This is used for both Room No and Official Name depending on category

        $json_path = storage_path('app/consumers.json');
        $consumers = [];

        if (file_exists($json_path)) {
            $consumers_db = json_decode(file_get_contents($json_path), true);
            if ($consumers_db) {
                foreach ($consumers_db as $c) {
                    $search_lower = strtolower($room);

                    if ($building === 'Official') {
                        // For Official, search in name, department, and building fields
                        $name_match = isset($c['name']) && stripos($c['name'], $room) !== false;
                        $dept_match = isset($c['department']) && stripos($c['department'], $room) !== false;
                        $build_match = isset($c['building']) && stripos($c['building'], 'Official') !== false;

                        if (($name_match || $dept_match) && $build_match) {
                            $consumers[] = $c;
                        }
                    } else {
                        // Standard search: Building must match and Room should match exactly
                        $b_match = isset($c['building']) && (stripos($c['building'], $building) !== false);
                        $r_match = isset($c['room']) && ((string) $c['room'] === (string) $room);

                        if ($b_match && $r_match) {
                            $consumers[] = $c;
                        }
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomFurniture;

class RoomFurnitureController extends Controller
{
    public function index()
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        if (session('role') !== 'admin' && session('role') !== 'staff') {
            return redirect()->route('dashboard');
        }

        $readings = RoomFurniture::latest()->get();
        return view('furniture_editor', compact('readings'));
    }

    public function store(Request $request)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $request->validate([
            'building' => 'required|string',
            'room' => 'required|string',
            'bed' => 'nullable|string',
            'table' => 'nullable|string',
            'chair' => 'nullable|string',
            'cupboard' => 'nullable|string',
        ]);

        RoomFurniture::create($request->only(['building', 'room', 'bed', 'table', 'chair', 'cupboard']));

        return redirect()->back()->with('success', 'Furniture record saved successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $request->validate([
            'building' => 'required|string',
            'room' => 'required|string',
            'bed' => 'nullable|string',
            'table' => 'nullable|string',
            'chair' => 'nullable|string',
            'cupboard' => 'nullable|string',
        ]);

        $record = RoomFurniture::findOrFail($id);
        $record->update($request->only(['building', 'room', 'bed', 'table', 'chair', 'cupboard']));

        return redirect()->back()->with('success', 'Furniture record updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $record = RoomFurniture::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'Furniture record deleted successfully!');
    }

    public function searchPublic(Request $request)
    {
        if (!$request->has('room')) {
            return redirect()->route('landing')->with('active_mode', 'room-furniture');
        }

        $room = $request->room;
        $building = $request->building;

        $query = RoomFurniture::query();
        if ($building) {
            $query->where('building', 'like', "%{$building}%");
        }
        if ($room) {
            $query->where('room', 'like', "%{$room}%");
        }
        
        $furnitures = $query->get();

        return view('landing', [
            'furnitures' => $furnitures,
            'active_mode' => 'room-furniture',
            'search_building' => $building,
            'search_room' => $room,
            'nextComplaintSno' => \App\Models\Complaint::count() + 1,
            'nextFurnitureSno' => \App\Models\FurnitureLog::count() + 1,
        ]);
    }

    public function import(Request $request)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('excel_file');

        if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            
            if (count($rows) <= 1) {
                return redirect()->back()->withErrors(['excel_file' => 'The uploaded file is empty or missing data.']);
            }

            $count = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; 
                
                if (!isset($row[0]) || trim($row[0]) === '') continue;

                $b = ucwords(strtolower(trim($row[0] ?? '')));
                $r = trim((string)($row[1] ?? ''));

                RoomFurniture::create([
                    'building' => $b,
                    'room' => $r,
                    'bed' => trim((string)($row[2] ?? '')),
                    'table' => trim((string)($row[3] ?? '')),
                    'chair' => trim((string)($row[4] ?? '')),
                    'cupboard' => trim((string)($row[5] ?? '')),
                ]);
                $count++;
            }

            return redirect()->back()->with('success', "Successfully imported {$count} furniture records!");
        } else {
            return redirect()->back()->withErrors(['excel_file' => 'Failed to parse the Excel file.']);
        }
    }
}

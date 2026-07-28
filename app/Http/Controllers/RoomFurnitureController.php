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
        if (!$request->has('building')) {
            return redirect()->route('landing')->with('active_mode', 'room-furniture');
        }

        $building = $request->building;
        $room = $request->room;

        $furnitures = RoomFurniture::where('building', $building)
            ->where('room', $room)
            ->get();

        return view('landing', [
            'furnitures' => $furnitures,
            'active_mode' => 'room-furniture',
            'search_building' => $building,
            'search_room' => $room,
            'nextComplaintSno' => \App\Models\Complaint::count() + 1,
            'nextFurnitureSno' => \App\Models\FurnitureLog::count() + 1,
        ]);
    }
}

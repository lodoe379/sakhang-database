<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shuchkin\SimpleXLSX;

use App\Models\MeterReading;

class MeterReadingController extends Controller
{
    public function index()
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        if (session('role') === 'staff') {
            return redirect()->route('dashboard');
        }

        $readings = MeterReading::latest()->get();
        return view('excel_editor', compact('readings'));
    }

    public function store(Request $request)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $request->validate([
            'building' => 'required|string',
            'room' => 'required|string',
            'consumer_id' => 'required|string',
            'in_id' => 'required|string',
            'meter_number' => 'required|string',
            'meter_image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('meter_image')) {
            $imagePath = $request->file('meter_image')->store('meters', 'public');
        }

        MeterReading::create([
            'building' => $request->building,
            'room' => $request->room,
            'consumer_id' => $request->consumer_id,
            'in_id' => $request->in_id,
            'meter_number' => $request->meter_number,
            'meter_image' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Meter reading saved successfully!');
    }

    public function update(Request $request, $id)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $reading = MeterReading::findOrFail($id);

        $request->validate([
            'building' => 'required|string',
            'room' => 'required|string',
            'consumer_id' => 'required|string',
            'in_id' => 'required|string',
            'meter_number' => 'required|string',
            'meter_image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('meter_image')) {
            $reading->meter_image = $request->file('meter_image')->store('meters', 'public');
        }

        $reading->update([
            'building' => $request->building,
            'room' => $request->room,
            'consumer_id' => $request->consumer_id,
            'in_id' => $request->in_id,
            'meter_number' => $request->meter_number,
        ]);

        return redirect()->back()->with('success', 'Meter reading updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $reading = MeterReading::findOrFail($id);
        $reading->delete();

        return redirect()->back()->with('success', 'Meter reading deleted successfully!');
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

        if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            
            if (count($rows) <= 1) {
                return redirect()->back()->withErrors(['excel_file' => 'The uploaded file is empty or missing data.']);
            }

            // Assume first row is headers. Skip index 0.
            $count = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; 
                
                // Expecting at least 5 columns: Building, Room, Consumer ID, IN ID, Meter Number
                if (!isset($row[0])) continue; // Skip empty rows

                MeterReading::create([
                    'building' => $row[0] ?? '',
                    'room' => (string)($row[1] ?? ''),
                    'consumer_id' => (string)($row[2] ?? ''),
                    'in_id' => (string)($row[3] ?? ''),
                    'meter_number' => (string)($row[4] ?? ''),
                    'meter_image' => null, 
                ]);
                $count++;
            }

            return redirect()->back()->with('success', "Successfully imported {$count} meter readings!");
        } else {
            return redirect()->back()->withErrors(['excel_file' => 'Failed to parse the Excel file.']);
        }
    }
}

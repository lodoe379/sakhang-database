<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FurnitureLog;

class FurnitureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'official_name' => 'required|string',
            'type' => 'required|string',
            'return_date' => 'nullable|date',
            'items' => 'required|array',
            'application' => 'required_if:type,Lend|file|max:10240',
        ]);

        // Filter valid items
        $items = collect($request->items)->filter(function ($item) {
            return !empty($item['name']) && !empty($item['qty']);
        })->values()->toArray();

        $appPath = null;
        if ($request->hasFile('application')) {
            $appPath = $request->file('application')->store('uploads', 'public');
        }

        FurnitureLog::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'official_name' => $request->official_name,
            'type' => $request->type,
            'return_date' => $request->return_date,
            'items' => $items,
            'application' => $appPath,
        ]);

        return redirect()->back()->with('message', 'Form filled successfully!');
    }

    public function update(\App\Models\FurnitureLog $furniture, Request $request)
    {
        if (!session('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'action_date' => 'nullable|date',
            'remark' => 'nullable|string'
        ]);

        $furniture->update($request->only(['action_date', 'remark']));

        return response()->json(['success' => true]);
    }

    public function toggle(\App\Models\FurnitureLog $furniture)
    {
        if (!session('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $furniture->update(['done' => !$furniture->done]);

        return response()->json(['success' => true, 'done' => $furniture->done]);
    }

    public function export()
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $filename = "furniture_records_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $records = FurnitureLog::all();

            // Calculate S.Nos
            $lendRecords = $records->where('type', 'Lend')->sortBy('created_at');
            $returnRecords = $records->where('type', 'Return')->sortBy('created_at');

            $lendSnos = [];
            $i = 1;
            foreach ($lendRecords as $r) {
                $lendSnos[$r->id] = $i++;
            }

            $returnSnos = [];
            $i = 1;
            foreach ($returnRecords as $r) {
                $returnSnos[$r->id] = $i++;
            }

            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper character encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['S.No', 'Date & Time', 'Action Date', 'Name', 'Department', 'Phone', 'Type', 'Estimated Return Date', 'Items', 'Remark', 'Status']);

            foreach ($records->sortByDesc('created_at') as $f) {
                // Format items string: "Item1 (x2), Item2 (x1)"
                $itemsStr = collect($f->items)->map(function ($item) {
                    return ($item['name'] ?? 'N/A') . " (x" . ($item['qty'] ?? '1') . ")";
                })->implode(', ');

                $sNo = ($f->type === 'Lend') ? ($lendSnos[$f->id] ?? '') : ($returnSnos[$f->id] ?? '');

                fputcsv($file, [
                    $sNo,
                    $f->created_at,
                    $f->action_date,
                    $f->name,
                    $f->official_name,
                    $f->phone,
                    $f->type,
                    $f->return_date,
                    $itemsStr,
                    $f->remark,
                    $f->done ? 'Done' : 'Pending'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

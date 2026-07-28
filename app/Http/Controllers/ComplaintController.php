<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function index()
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        if (session('role') !== 'admin' && session('role') !== 'staff1') {
            return redirect()->route('dashboard');
        }

        $complaints = Complaint::latest()->get();

        return view('electrical_complaints', [
            'complaints' => $complaints,
        ]);
    }

    public function import(Request $request)
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        if ( $xlsx = \Shuchkin\SimpleXLSX::parse( $request->file('file')->path() ) ) {
            $isHeader = true;
            foreach ( $xlsx->rows() as $row ) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }
                
                if (count($row) < 8 || empty($row[3]) || empty($row[6])) continue;

                $done = false;
                if (isset($row[10]) && strtolower($row[10]) === 'done') {
                    $done = true;
                }
                
                $id = !empty($row[0]) ? (int) $row[0] : null;
                $data = [
                    'action_date' => !empty($row[2]) ? date('Y-m-d', strtotime($row[2])) : null,
                    'name' => $row[3],
                    'building' => $row[4],
                    'room' => $row[5],
                    'phone' => $row[6],
                    'complaint' => $row[7],
                    'user_reply' => $row[8] ?? null,
                    'remark' => $row[9] ?? null,
                    'done' => $done,
                    'signature' => (isset($row[11]) && strtolower($row[11]) == 'signed') ? 'imported_signature' : null,
                ];

                if ($id) {
                    Complaint::updateOrCreate(['id' => $id], $data);
                } else {
                    Complaint::create($data);
                }
            }
            return redirect()->route('complaints.index')->with('success', 'Complaints imported successfully!');
        } else {
            return redirect()->route('complaints.index')->with('error', \Shuchkin\SimpleXLSX::parseError());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'building' => 'required|string',
            'room' => 'required|string',
            'complaint' => 'required|string',
            'image' => 'required|image|max:10240',
            'video' => 'nullable|mimes:mp4,mov,avi|max:20480',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('uploads', 'public');
        }

        Complaint::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'building' => $request->building,
            'room' => $request->room,
            'complaint' => $request->complaint,
            'image' => $imagePath,
            'video' => $videoPath,
            'signature' => $request->signature,
            'done' => false,
        ]);

        return redirect()->route('landing')->with('message', 'Form filled successfully!')->with('show_emergency', true);
    }

    public function toggle(Complaint $complaint)
    {
        if (!session('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $complaint->update(['done' => !$complaint->done]);
        return response()->json(['success' => true, 'done' => $complaint->done]);
    }

    public function update(Complaint $complaint, Request $request)
    {
        if (!session('loggedin')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'action_date' => 'nullable|date',
            'remark' => 'nullable|string'
        ]);

        $complaint->update($request->only(['action_date', 'remark']));

        return response()->json(['success' => true]);
    }

    public function checkStatus(Request $request)
    {
        if (!$request->has('phone')) {
            return redirect()->route('landing')->with('active_mode', 'status');
        }

        $request->validate([
            'phone' => 'required|string',
            'year' => 'nullable|integer',
        ]);

        $year = $request->year ?? date('Y');

        $status_complaints = Complaint::where('phone', $request->phone)
            ->whereYear('created_at', $year)
            ->where('done', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $status_furniture = \App\Models\FurnitureLog::where('phone', $request->phone)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $nextComplaintSno = Complaint::count() + 1;
        $nextFurnitureSno = \App\Models\FurnitureLog::count() + 1;
        $search_year = $year;
        $search_phone = $request->phone;

        return view('landing', compact('status_complaints', 'status_furniture', 'nextComplaintSno', 'nextFurnitureSno', 'search_year', 'search_phone'))
            ->with('active_mode', 'status');
    }
    public function storeReply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'user_reply' => 'required|string|max:1000',
        ]);

        $complaint->update(['user_reply' => $request->user_reply]);

        return back()->with('message', 'Reply sent successfully!');
    }

    public function export()
    {
        if (!session('loggedin')) {
            return redirect()->route('landing');
        }

        $filename = "electrical_complaints_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $complaints = Complaint::all();
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper character encoding in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['ID', 'Date & Time', 'Action Date', 'Name', 'Building', 'Room', 'Phone', 'Complaint Details', 'User Reply', 'Remark', 'Status', 'Signature']);

            foreach ($complaints as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->created_at,
                    $c->action_date,
                    $c->name,
                    $c->building,
                    $c->room,
                    $c->phone,
                    $c->complaint,
                    $c->user_reply,
                    $c->remark,
                    $c->done ? 'Done' : 'Pending',
                    $c->signature ? 'Signed' : 'Not Signed'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

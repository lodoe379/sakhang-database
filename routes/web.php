<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\FurnitureController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeterReadingController;
use App\Http\Controllers\RoomFurnitureController;
use App\Http\Controllers\RoomDataController;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function (Request $request) {
    $nextComplaintSno = \App\Models\Complaint::count() + 1;
    $nextLendSno = \App\Models\FurnitureLog::where('type', 'Lend')->count() + 1;
    $nextReturnSno = \App\Models\FurnitureLog::where('type', 'Return')->count() + 1;
    $active_mode = $request->query('mode');
    if ($active_mode) {
        return view('landing', compact('nextComplaintSno', 'nextLendSno', 'nextReturnSno', 'active_mode'));
    }
    return view('landing', compact('nextComplaintSno', 'nextLendSno', 'nextReturnSno'));
})->name('landing');

Route::get('/login', function () {
    return redirect()->route('landing', ['mode' => 'admin']);
})->name('login');
Route::post('/login', [DashboardController::class, 'login']);
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

Route::post('/submit-complaint', [ComplaintController::class, 'store'])->name('complaint.store');
Route::post('/submit-furniture', [FurnitureController::class, 'store'])->name('furniture.store');
Route::get('/search-consumer', [ConsumerController::class, 'search'])->name('consumer.search');
Route::get('/search-furniture', [RoomFurnitureController::class, 'searchPublic'])->name('furniture.search');
Route::get('/complaint-status', [ComplaintController::class, 'checkStatus'])->name('complaint.status');
Route::post('/complaint/reply/{complaint}', [ComplaintController::class, 'storeReply'])->name('complaint.reply');

Route::group([], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-room', [DashboardController::class, 'updateRoom'])->name('dashboard.updateRoom');
    Route::post('/dashboard/delete-room', [DashboardController::class, 'deleteRoom'])->name('dashboard.deleteRoom');
    Route::post('/dashboard/save-room-row', [DashboardController::class, 'saveRoomRow'])->name('dashboard.saveRoomRow');
    Route::get('/dashboard/search-room', [DashboardController::class, 'searchRoomData'])->name('dashboard.searchRoom');
    Route::get('/furniture-management', [DashboardController::class, 'furnitureManagement'])->name('furniture.management');
    Route::get('/complaints/export', [ComplaintController::class, 'export'])->name('complaints.export');
    Route::get('/furniture/export', [FurnitureController::class, 'export'])->name('furniture.export');
    Route::post('/complaint/toggle/{complaint}', [ComplaintController::class, 'toggle'])->name('complaint.toggle');
    Route::post('/complaint/update/{complaint}', [ComplaintController::class, 'update'])->name('complaint.update');
    Route::post('/furniture/update/{furniture}', [FurnitureController::class, 'update'])->name('furniture.update');
    Route::post('/furniture/toggle/{furniture}', [FurnitureController::class, 'toggle'])->name('furniture.toggle');
    
    // Standalone Electrical Complaints Page
    Route::get('/electrical-complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/electrical-complaints/import', [ComplaintController::class, 'import'])->name('complaints.import');
    
    // Standalone Room Data Page
    Route::get('/room-data', [RoomDataController::class, 'index'])->name('room.data');
    
    // Meter Readings (formerly Excel Editor Module)
    Route::get('/excel-editor', [MeterReadingController::class, 'index'])->name('excel.editor');
    Route::post('/excel-editor', [MeterReadingController::class, 'store'])->name('excel.editor.store');
    Route::post('/excel-editor/update/{id}', [MeterReadingController::class, 'update'])->name('excel.editor.update');
    Route::get('/excel-editor/update/{id}', function () { return redirect()->route('excel.editor'); });
    Route::post('/excel-editor/delete/{id}', [MeterReadingController::class, 'destroy'])->name('excel.editor.destroy');
    Route::get('/excel-editor/delete/{id}', function () { return redirect()->route('excel.editor'); });
    Route::post('/excel-editor/import', [MeterReadingController::class, 'import'])->name('excel.editor.import');
    Route::get('/excel-editor/import', function () {
        return redirect()->route('excel.editor');
    });

    // Furniture List Editor
    Route::get('/furniture-editor', [RoomFurnitureController::class, 'index'])->name('furniture.editor');
    Route::post('/furniture-editor', [RoomFurnitureController::class, 'store'])->name('furniture.editor.store');
    Route::post('/furniture-editor/update/{id}', [RoomFurnitureController::class, 'update'])->name('furniture.editor.update');
    Route::post('/furniture-editor/delete/{id}', [RoomFurnitureController::class, 'destroy'])->name('furniture.editor.destroy');
});

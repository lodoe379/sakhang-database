@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    body {
        overflow: hidden; /* Prevent body scroll */
    }
    .inner-paper {
        height: 100vh;
        max-width: 100%;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        border-radius: 0;
    }
    .dashboard-header {
        flex: 0 0 auto;
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 16px 30px; 
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 10;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    .dashboard-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        position: relative;
    }
    #dashboard-view, #complaint-table-view, #furniture-table-view, #room-data-table-view {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .table-wrapper {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    /* Simple scrollbar for cleaner look */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection

@section('content')
    <div class="inner-paper">
        <!-- Header Section -->
        <div class="dashboard-header">
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.02em;">{{ str_starts_with(session('role'), 'staff') ? 'Staff Dashboard' : 'Admin Dashboard' }}</h2>
            <div style="display: flex; gap: 10px; align-items: center;">

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn"
                        style="width: auto; padding: 6px 16px; font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1); color: white; font-weight: 500; transition: all 0.2s; border-radius: 8px;"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</button>
                </form>
            </div>
        </div>

        <div class="dashboard-body">


        <!-- Dashboard Content -->
        <div id="dashboard-view">
            <!-- Dashboard Summary Boxes -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 10px;">
                
                @if(session('role') === 'admin' || session('role') === 'staff1')
                <!-- Electrical Complaint Box -->
                <div onclick="window.location.href='{{ route('complaints.index') }}'"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.8; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>⚡</span> ELECTRICAL COMPLAINTS
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: end;">
                        <div style="text-align: left;">
                            <div style="font-size: 32px; font-weight: 900; line-height: 1;">{{ $total_complaints }}</div>
                            <div style="font-size: 9px; opacity: 0.6; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Total</div>
                        </div>
                        <div style="text-align: left;">
                            <div id="count-done" style="font-size: 32px; font-weight: 900; line-height: 1; color: #84cc16;">{{ $done_complaints }}</div>
                            <div style="font-size: 9px; opacity: 0.6; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Resolved</div>
                        </div>
                        <div style="text-align: left;">
                            <div id="count-incomplete" style="font-size: 32px; font-weight: 900; line-height: 1; color: #fbce04;">{{ $incomplete_complaints }}</div>
                            <div style="font-size: 9px; opacity: 0.6; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Pending</div>
                        </div>
                    </div>
                </div>

                @endif

                <!-- Empty Room Box -->
                <div onclick="showRoomDataTable()"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #8b5cf6 0%, #4c1d95 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🏢</span> EMPTY ROOMS SEARCH
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 10px; align-items: end;">
                        <div style="text-align: left;">
                            <div id="dashboard-empty-room-count" style="font-size: 32px; font-weight: 900; line-height: 1;">{{ $total_rooms }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Total</div>
                        </div>
                    </div>
                </div>

                @if(session('role') === 'admin' || session('role') === 'staff')

                <!-- Furniture Lend Box -->
                <div onclick="showFurnitureTable('Lend')"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🪑</span> FURNITURE LEND
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: end;">
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'all')">
                            <div style="font-size: 32px; font-weight: 900; line-height: 1;">{{ $total_lend }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Total</div>
                        </div>
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'done')">
                            <div id="count-lend-done" style="font-size: 32px; font-weight: 900; line-height: 1; color: #ffffff;">{{ $lend_done }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Done</div>
                        </div>
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'pending')">
                            <div id="count-lend-pending" style="font-size: 32px; font-weight: 900; line-height: 1; color: #ffffff; opacity: 0.6;">{{ $lend_pending }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Pending</div>
                        </div>
                    </div>
                </div>

                <!-- Furniture Return Box -->
                <div onclick="showFurnitureTable('Return')"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🔄</span> FURNITURE RETURN
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: end;">
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Return', 'all')">
                            <div style="font-size: 32px; font-weight: 900; line-height: 1;">{{ $total_return }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Total</div>
                        </div>
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Return', 'done')">
                            <div id="count-return-done" style="font-size: 32px; font-weight: 900; line-height: 1; color: #ffffff;">{{ $return_done }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Done</div>
                        </div>
                        <div style="text-align: left;" onclick="event.stopPropagation(); showFurnitureTable('Return', 'pending')">
                            <div id="count-return-pending" style="font-size: 32px; font-weight: 900; line-height: 1; color: #ffffff; opacity: 0.6;">{{ $return_pending }}</div>
                            <div style="font-size: 9px; opacity: 0.7; font-weight: 800; text-transform: uppercase; margin-top: 4px; letter-spacing: 1px;">Pending</div>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('role') === 'admin' || session('role') === 'staff1')
                <!-- Meter Readings Box -->
                <div onclick="window.location.href='{{ route('excel.editor') }}'"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>📋</span> Meter Data
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 46px;">
                        <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">Record Meter Data</div>
                        <div style="font-size: 10px; opacity: 0.7; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">Click to access form</div>
                    </div>
                </div>

                @endif

                @if(session('role') === 'admin' || session('role') === 'staff')
                <!-- Furniture List Editor Box -->
                <div onclick="window.location.href='{{ route('furniture.editor') }}'"
                    class="glass-card animate-fade-in"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); padding: 25px; color: white; cursor: pointer; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🪑</span> Furniture List
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 46px;">
                        <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">Record Furniture List</div>
                        <div style="font-size: 10px; opacity: 0.7; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">Click to access form</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- (Closed dashboard-view below) -->
        </div>

        <!-- Complaint Table View removed, now in electrical_complaints.blade.php -->

        <!-- Room Data Master Table View -->
        <div id="room-data-table-view" style="display: none; height: 100%; flex-direction: column;" class="py-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #7c3aed; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">🏢</div>
                    <h4 style="margin: 0; font-size: 16px; color: #1e293b;" id="room-table-title">Empty Room (<span id="empty-room-count">{{ count($roomData) }}</span>)</h4>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">

                    <button onclick="openAddRoomModal()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #8b5cf6; border: none; color: white; border-radius: 8px; font-weight: 600;">➕ Empty Room</button>
                    <button onclick="window.location.reload()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #059669; border: none; color: white; border-radius: 8px; font-weight: 600;">↻ Refresh</button>
                    <button onclick="showDashboard()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #1e3a5f; border: none; color: white; border-radius: 8px; font-weight: 600;">← Back</button>
                </div>
            </div>

            <div class="card p-2" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                <div class="table-wrapper">
                    <table class="complaint-table table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Building Name</th>
                                <th>Room</th>
                                <th>Bed</th>
                                <th>Table</th>
                                <th>Chair</th>
                                <th>Cupboard</th>
                                <th>Name on the bill</th>
                                <th>IN ID</th>
                                <th>Meter Number</th>
                                <th>Consumer ID</th>
                                <th>Account No</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomData as $room)
                                <tr class="complaint-row" style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="font-weight: 600; color: #0f172a;">{{ $room['building'] }}</td>
                                    <td style="font-weight: 600; color: #0f172a;">{{ $room['room'] }}</td>
                                    <td><input type="text" data-field="bed" value="{{ $room['bed'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'bed', this.value)" style="width: 100%; min-width: 50px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="table" value="{{ $room['table'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'table', this.value)" style="width: 100%; min-width: 50px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="chair" value="{{ $room['chair'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'chair', this.value)" style="width: 100%; min-width: 50px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="cupboard" value="{{ $room['cupboard'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'cupboard', this.value)" style="width: 100%; min-width: 50px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="name_on_bill" value="{{ $room['name_on_bill'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'name_on_bill', this.value)" style="width: 100%; min-width: 100px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="in_id" value="{{ $room['in_id'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'in_id', this.value)" style="width: 100%; min-width: 80px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="meter_number" value="{{ $room['meter_number'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'meter_number', this.value)" style="width: 100%; min-width: 80px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="consumer_id" value="{{ $room['consumer_id'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'consumer_id', this.value)" style="width: 100%; min-width: 80px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td><input type="text" data-field="account_no" value="{{ $room['account_no'] }}" onchange="updateRoomData('{{ $room['building'] }}', '{{ $room['room'] }}', 'account_no', this.value)" style="width: 100%; min-width: 80px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                                    <td>
                                        @if($room['meter_image'])
                                            <a href="{{ asset('storage/' . $room['meter_image']) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $room['meter_image']) }}" alt="Meter Image" style="max-width: 50px; max-height: 50px; border-radius: 4px;">
                                            </a>
                                        @else
                                            <span style="color: #94a3b8; font-size: 12px; font-style: italic;">No Image</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; gap: 8px;">
                                        <button onclick="saveRoomRowData(this, '{{ addslashes($room['building']) }}', '{{ addslashes($room['room']) }}')" style="color: #10b981; border: none; background: transparent; cursor: pointer; font-size: 16px;" title="Save Room">💾</button>
                                        <button onclick="deleteRoomData(this, '{{ addslashes($room['building']) }}', '{{ addslashes($room['room']) }}')" style="color: #ef4444; border: none; background: transparent; cursor: pointer; font-size: 16px;" title="Delete Room">🗑️</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Furniture Table View -->
        <div id="furniture-table-view" style="display: none; height: 100%; flex-direction: column;" class="py-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #f093fb; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">🪑</div>
                    <h4 style="margin: 0; font-size: 16px; color: #1e293b;" id="furniture-table-title">Furniture Records</h4>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('furniture.export') }}" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #d97706; border: none; color: white; border-radius: 8px; font-weight: 600; text-decoration: none;">📥 Excel</a>
                    <button onclick="window.location.reload()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #059669; border: none; color: white; border-radius: 8px; font-weight: 600;">↻ Refresh</button>
                    <button onclick="showDashboard()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #1e3a5f; border: none; color: white; border-radius: 8px; font-weight: 600;">← Back</button>
                </div>
            </div>

            <div class="card p-2" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                <div class="table-wrapper">

                    <table class="complaint-table table-bordered w-100">
                        <thead>
                            <tr>
                                <th style="width: 50px;">S.No</th>
                                <th style="width: 140px;">Date & Time</th>
                                <th style="width: 120px;">Action Date</th>
                                <th>Personnel</th>
                                <th>Designation</th>
                                <th style="width: 80px;">Type</th>
                                <th style="width: 120px;">Est. Return</th>
                                <th>Items</th>
                                <th>Remarks</th>
                                <th style="width: 50px;">Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($furniture as $f)
                                <tr class="furniture-row" data-type="{{ $f->type }}" data-done="{{ $f->done ? '1' : '0' }}">
                                    <td>{{ $f->s_no }}</td>
                                    <td>{{ $f->created_at->format('d/m/y H:i') }}</td>
                                    <td>
                                        <input type="date" value="{{ $f->action_date }}" 
                                            onchange="updateFurniture({{ $f->id }}, 'action_date', this.value)"
                                            style="padding: 4px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 13px; width: 100%;">
                                    </td>
                                    <td>{{ $f->name }}</td>
                                    <td>{{ $f->official_name }}</td>
                                    <td>
                                        <span style="padding: 2px 6px; border-radius: 4px; background: {{ $f->type == 'Lend' ? '#fff3f3' : '#f0fff4' }}; color: {{ $f->type == 'Lend' ? '#e53e3e' : '#2f855a' }}; font-weight: 700; font-size: 11px;">{{ $f->type }}</span>
                                    </td>
                                    <td>{{ $f->return_date }}</td>
                                    <td>
                                        @if($f->items)
                                            @foreach($f->items as $item)
                                                <div style="white-space: nowrap;">{{ $item['name'] }} ({{ $item['qty'] }})</div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <textarea onchange="updateFurniture({{ $f->id }}, 'remark', this.value)"
                                            style="width: 100%; border: 1px solid #e2e8f0; border-radius: 4px; padding: 4px; font-size: 13px; min-height: 40px;">{{ $f->remark }}</textarea>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="checkbox" onchange="toggleFurnitureDone({{ $f->id }}, this)" {{ $f->done ? 'checked' : '' }}
                                            style="width: 20px; height: 20px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

        <!-- Add Room Modal (Advanced Search) -->
        <div id="add-room-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 100; align-items: center; justify-content: center;">
            <div class="glass-panel" style="background: white; border-radius: 16px; padding: 30px; width: 90%; max-width: 450px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
                    <h3 style="margin: 0; font-size: 20px; display: flex; align-items: center; gap: 10px; color: #1e293b;">
                        <span style="font-size: 24px;">🔍</span> Empty Room Search
                    </h3>
                    <button onclick="closeAddRoomModal()" style="background: transparent; border: none; font-size: 20px; cursor: pointer; color: #64748b;">✖</button>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Building Name</label>
                    <select id="modal-building-select" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box;">
                        <option value="">-- Select Building --</option>
                        @php
                            $b1 = config('app_data.buildings', []);
                            $b2 = \App\Models\MeterReading::distinct()->pluck('building')->toArray();
                            $b3 = \App\Models\RoomFurniture::distinct()->pluck('building')->toArray();
                            
                            $raw_buildings = array_filter(array_merge($b1, $b2, $b3));
                            $comp_buildings = [];
                            $seen = [];
                            foreach ($raw_buildings as $b) {
                                $lower = strtolower(trim($b));
                                if (!isset($seen[$lower])) {
                                    $seen[$lower] = true;
                                    $comp_buildings[] = trim($b);
                                }
                            }
                            sort($comp_buildings, SORT_NATURAL | SORT_FLAG_CASE);
                        @endphp
                        @foreach($comp_buildings as $b)
                            @if(!empty(trim($b)))
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase;">Room</label>
                    @php
                        $r1 = \App\Models\MeterReading::distinct()->pluck('room')->toArray();
                        $r2 = \App\Models\RoomFurniture::distinct()->pluck('room')->toArray();
                        $r3 = \App\Models\EmptyRoom::distinct()->pluck('room')->toArray();
                        $modal_rooms = array_unique(array_merge($r1, $r2, $r3));
                        sort($modal_rooms);
                    @endphp
                    <input type="text" id="modal-room-input" list="room-list" placeholder="Enter or Select Room" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; outline: none; box-sizing: border-box;">
                    <datalist id="room-list" style="display: none;">
                        @foreach($modal_rooms as $r)
                            @if(!empty($r))
                                <option value="{{ $r }}"></option>
                            @endif
                        @endforeach
                    </datalist>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button onclick="searchAndAddRoom()" style="flex: 1; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.2s;">Search</button>
                    <button onclick="closeAddRoomModal()" style="flex: 1; padding: 12px; background: #64748b; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.2s;">Clear & Close</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function showFurnitureTable(type = null, statusFilter = 'all') {
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('furniture-table-view').style.display = 'block';

            const rows = document.querySelectorAll('.furniture-row');
            rows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                const isDone = row.getAttribute('data-done') === '1';
                
                const typeMatch = !type || rowType === type;
                let statusMatch = true;

                if (statusFilter === 'done') statusMatch = isDone;
                else if (statusFilter === 'pending') statusMatch = !isDone;

                if (typeMatch && statusMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const title = document.getElementById('furniture-table-title');
            let baseTitle = 'Furniture Records';
            if (type === 'Lend') baseTitle = 'Furniture Lend Records';
            else if (type === 'Return') baseTitle = 'Furniture Return Records';

            if (statusFilter === 'done') title.innerText = baseTitle + ' (Done)';
            else if (statusFilter === 'pending') title.innerText = baseTitle + ' (Pending)';
            else title.innerText = baseTitle;
        }

        function showDashboard() {
            document.getElementById('dashboard-view').style.display = 'block';
            document.getElementById('furniture-table-view').style.display = 'none';
            document.getElementById('room-data-table-view').style.display = 'none';
        }

        function showRoomDataTable() {
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('furniture-table-view').style.display = 'none';
            document.getElementById('room-data-table-view').style.display = 'flex';
        }

        function toggleFurnitureFields(value) {
            const lendFields = document.getElementById('lend-fields-dash');
            const applicationInput = document.querySelector('input[name="application"]');
            if (value === 'Lend') {
                lendFields.style.display = 'grid';
                applicationInput.required = true;
            } else {
                lendFields.style.display = 'none';
                applicationInput.required = false;
            }
        }

        let furnitureRowCountDash = 1;
        function addFurnitureRowDash() {
            if (furnitureRowCountDash >= 5) return;
            
            const container = document.getElementById('furniture-items-container-dash');
            const newRow = document.createElement('div');
            newRow.className = 'furniture-item-row';
            newRow.style.cssText = 'display: flex; gap: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 10px;';
            newRow.innerHTML = `
                <input type="text" name="items[${furnitureRowCountDash}][name]" required placeholder="Item ${furnitureRowCountDash + 1} name" style="flex: 4; border: none; border-bottom: 2px solid #f1f5f9; padding: 8px; outline: none;">
                <input type="number" name="items[${furnitureRowCountDash}][qty]" required placeholder="Qty" style="flex: 1; border: none; border-bottom: 2px solid #f1f5f9; padding: 8px; outline: none;" min="1">
            `;
            container.appendChild(newRow);
            furnitureRowCountDash++;
            
            if (furnitureRowCountDash >= 5) {
                const btn = document.getElementById('add-item-btn-dash');
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
                btn.innerText = 'MAX ITEMS REACHED';
                btn.onclick = null;
            }
        }

        function updateRoomData(building, room, field, value) {
            saveData('/dashboard/update-room', { building, room, field, value });
        }

        function deleteRoomData(btnElement, building, room) {
            if (!confirm(`Are you sure you want to delete all records for Building: ${building}, Room: ${room}?`)) {
                return;
            }

            fetch('/dashboard/delete-room', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ building, room })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const tr = btnElement.closest('tr');
                    if (tr) tr.remove();
                    
                    // Update count
                    const countSpan = document.getElementById('empty-room-count');
                    if (countSpan) countSpan.innerText = Math.max(0, parseInt(countSpan.innerText || 1) - 1);
                    
                    const dashboardCountSpan = document.getElementById('dashboard-empty-room-count');
                    if (dashboardCountSpan) dashboardCountSpan.innerText = Math.max(0, parseInt(dashboardCountSpan.innerText || 1) - 1);
                } else {
                    alert('Failed to delete room data.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while deleting.');
            });
        }

        function openAddRoomModal() {
            document.getElementById('modal-building-select').value = '';
            document.getElementById('modal-room-input').value = '';
            document.getElementById('add-room-modal').style.display = 'flex';
        }

        function closeAddRoomModal() {
            document.getElementById('add-room-modal').style.display = 'none';
        }


        function searchAndAddRoom() {
            const building = document.getElementById('modal-building-select').value;
            const room = document.getElementById('modal-room-input').value.trim();
            
            if (!building || !room) {
                alert('Please select a building and enter a room number.');
                return;
            }

            const btn = document.querySelector('#add-room-modal button[onclick="searchAndAddRoom()"]');
            const originalText = btn.innerText;
            btn.innerText = 'Searching...';
            btn.disabled = true;

            fetch(`/dashboard/search-room?building=${encodeURIComponent(building)}&room=${encodeURIComponent(room)}`)
                .then(res => res.json())
                .then(data => {
                    // Update count
                    const countSpan = document.getElementById('empty-room-count');
                    if (countSpan) countSpan.innerText = parseInt(countSpan.innerText || 0) + 1;
                    
                    const dashboardCountSpan = document.getElementById('dashboard-empty-room-count');
                    if (dashboardCountSpan) dashboardCountSpan.innerText = parseInt(dashboardCountSpan.innerText || 0) + 1;

                    closeAddRoomModal();
                    
                    const tbody = document.querySelector('#room-data-table-view tbody');
                    const newRowId = 'new-room-' + Date.now();
                    
                    // Use fetched data or empty defaults if no records found
                    const bed = data.bed || '';
                    const table = data.table || '';
                    const chair = data.chair || '';
                    const cupboard = data.cupboard || '';
                    const name_on_bill = data.name_on_bill || '';
                    const in_id = data.in_id || '';
                    const meter_number = data.meter_number || '';
                    const consumer_id = data.consumer_id || '';
                    const account_no = data.account_no || '';
                    const meter_image = data.meter_image || '';
                    
                    const storageBaseUrl = "{{ asset('storage') }}";
                    const imageHtml = meter_image ? 
                        `<a href="${storageBaseUrl}/${meter_image}" target="_blank"><img src="${storageBaseUrl}/${meter_image}" alt="Meter Image" style="max-width: 50px; max-height: 50px; border-radius: 4px;"></a>` : 
                        '';

                    const html = `
                        <tr class="complaint-row animate-fade-in" style="border-bottom: 1px solid #f1f5f9; background-color: #f0fdf4;">
                            <td><input type="text" id="${newRowId}-building" value="${building}" readonly style="width: 100%; min-width: 80px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" id="${newRowId}-room" value="${room}" readonly style="width: 100%; min-width: 50px; border: none; background: transparent; padding: 4px; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="bed" value="${bed}" onchange="updateDynamicRoom('${newRowId}', 'bed', this.value)" style="width: 100%; min-width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="table" value="${table}" onchange="updateDynamicRoom('${newRowId}', 'table', this.value)" style="width: 100%; min-width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="chair" value="${chair}" onchange="updateDynamicRoom('${newRowId}', 'chair', this.value)" style="width: 100%; min-width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="cupboard" value="${cupboard}" onchange="updateDynamicRoom('${newRowId}', 'cupboard', this.value)" style="width: 100%; min-width: 50px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="name_on_bill" value="${name_on_bill}" onchange="updateDynamicRoom('${newRowId}', 'name_on_bill', this.value)" style="width: 100%; min-width: 100px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="in_id" value="${in_id}" onchange="updateDynamicRoom('${newRowId}', 'in_id', this.value)" style="width: 100%; min-width: 80px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="meter_number" value="${meter_number}" onchange="updateDynamicRoom('${newRowId}', 'meter_number', this.value)" style="width: 100%; min-width: 80px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="consumer_id" value="${consumer_id}" onchange="updateDynamicRoom('${newRowId}', 'consumer_id', this.value)" style="width: 100%; min-width: 80px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td><input type="text" data-field="account_no" value="${account_no}" onchange="updateDynamicRoom('${newRowId}', 'account_no', this.value)" style="width: 100%; min-width: 80px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px; background: white; color: #0f172a; font-weight: 600;"></td>
                            <td>${imageHtml || '<span style="color: #94a3b8; font-size: 12px; font-style: italic;">No Image</span>'}</td>
                            <td style="display: flex; gap: 8px;">
                                <button onclick="saveRoomRowData(this, '${building.replace(/'/g, "\\'")}', '${room.replace(/'/g, "\\'")}')" style="color: #10b981; border: none; background: transparent; cursor: pointer; font-size: 16px;" title="Save Room">💾</button>
                                <button onclick="deleteRoomData(this, '${building.replace(/'/g, "\\'")}', '${room.replace(/'/g, "\\'")}')" style="color: #ef4444; border: none; background: transparent; cursor: pointer; font-size: 16px;" title="Delete Room">🗑️</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('afterbegin', html);
                })
                .catch(err => {
                    console.error(err);
                    alert('Error searching room data.');
                })
                .finally(() => {
                    btn.innerText = originalText;
                    btn.disabled = false;
                });
        }

        function updateDynamicRoom(rowId, field, value) {
            const building = document.getElementById(rowId + '-building').value.trim();
            const room = document.getElementById(rowId + '-room').value.trim();
            
            if (!building || !room) {
                alert('Please enter Building Name and Room Number first.');
                return;
            }
            
            updateRoomData(building, room, field, value);
        }

        function saveRoomRowData(btnElement, building, room) {
            const tr = btnElement.closest('tr');
            if (!tr) return;

            const payload = { building, room };
            tr.querySelectorAll('input[data-field]').forEach(input => {
                payload[input.dataset.field] = input.value;
            });

            const originalHtml = btnElement.innerHTML;
            btnElement.innerHTML = '⏳';
            btnElement.disabled = true;

            fetch('/dashboard/save-room-row', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btnElement.innerHTML = '✅';
                    setTimeout(() => { btnElement.innerHTML = originalHtml; btnElement.disabled = false; }, 2000);
                } else {
                    alert('Failed to save room row.');
                    btnElement.innerHTML = originalHtml;
                    btnElement.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while saving.');
                btnElement.innerHTML = originalHtml;
                btnElement.disabled = false;
            });
        }

        function updateFurniture(id, field, value) {
            saveData(`/furniture/update/${id}`, { [field]: value });
        }

        function toggleFurnitureDone(id, checkbox) {
            const isChecked = checkbox.checked;
            const row = checkbox.closest('tr');
            const type = row.getAttribute('data-type'); // 'Lend' or 'Return'
            
            fetch(`/furniture/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.setAttribute('data-done', isChecked ? '1' : '0');
                    
                    // Update appropriate counters
                    const typeLower = type.toLowerCase();
                    const doneCountEl = document.getElementById(`count-${typeLower}-done`);
                    const pendingCountEl = document.getElementById(`count-${typeLower}-pending`);
                    
                    if (doneCountEl && pendingCountEl) {
                        let doneCount = parseInt(doneCountEl.innerText);
                        let pendingCount = parseInt(pendingCountEl.innerText);
                        
                        if (isChecked) {
                            doneCount++;
                            pendingCount--;
                        } else {
                            doneCount--;
                            pendingCount++;
                        }
                        
                        doneCountEl.innerText = doneCount;
                        pendingCountEl.innerText = pendingCount;
                    }
                } else {
                    checkbox.checked = !isChecked;
                    alert(data.error || 'Failed to update furniture status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkbox.checked = !isChecked;
            });
        }


        // complaint functions removed


        function saveData(url, data) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to save data. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving.');
            });
        }
    </script>
@endsection
@extends('layouts.app')

@section('title', 'Master Room Data')

@section('styles')
<style>
    body {
        overflow: hidden; 
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
        padding: 12px 25px; 
        background: #000000;
        z-index: 10;
    }
    .dashboard-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        position: relative;
    }
    #complaint-table-view {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .table-wrapper {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
    }
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

    /* Modal Styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); 
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 500px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>
@endsection

@section('content')
    <div class="inner-paper">
        <div class="dashboard-header">
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.02em;">Electrical Complaints Management</h2>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('dashboard') }}" class="btn"
                    style="width: auto; padding: 6px 16px; font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1); color: white; font-weight: 500; transition: all 0.2s; border-radius: 8px; text-decoration: none;"
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'">← Dashboard</a>
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
            @if(session('success'))
                <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                    {{ session('error') }}
                </div>
            @endif

        <div id="dashboard-view" style="display: flex; height: 100%; flex-direction: column;" class="py-2 px-4">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #7c3aed; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">🏢</div>
                    <h4 style="margin: 0; font-size: 16px;" id="table-title">Master Room Data (<span id="empty-room-count">{{ count($roomData) }}</span>)</h4>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">

                    <button onclick="openAddRoomModal()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #8b5cf6; border: none; color: white; border-radius: 8px; font-weight: 600;">➕ Empty Room</button>
                    <button onclick="window.location.reload()" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #059669; border: none; color: white; border-radius: 8px; font-weight: 600;">↻ Refresh</button>
                    <button onclick="window.location.href='{{ route('dashboard') }}'" class="btn"
                        style="width: auto; padding: 6px 12px; font-size: 12px; background: #1e3a5f; border: none; color: white; border-radius: 8px; font-weight: 600;">← Back to Dashboard</button>
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
                            $comp_buildings = config('app_data.buildings', []);
                            $comp_buildings = array_unique($comp_buildings);
                            sort($comp_buildings);
                        @endphp
                        @foreach($comp_buildings as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
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

    <script>
        function updateRoomData(building, room, field, value) {
            fetch('/dashboard/update-room', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ building, room, field, value })
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

                    closeAddRoomModal();
                    
                    const tbody = document.querySelector('.complaint-table tbody');
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
                            <td></td>
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
    </script>
@endsection

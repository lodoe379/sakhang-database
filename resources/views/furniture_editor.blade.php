@extends('layouts.app')

@section('title', 'Furniture List')

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
        background: #f1f5f9;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
    }
    .table-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
    }
</style>
@endsection

@section('content')
    <div class="inner-paper">
        <div class="dashboard-header">
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.02em;">Furniture List Management</h2>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('dashboard') }}" class="btn"
                    style="width: auto; padding: 6px 16px; font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1); color: white; font-weight: 500; transition: all 0.2s; border-radius: 8px; text-decoration: none;"
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'">← Back</a>
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

            @if ($errors->any())
                <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                
                <!-- Form Section -->
                <div style="background: white; border-radius: 20px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                            <span>🪑</span> Room Furniture
                        </h3>
                    </div>
                    

                    <form action="{{ route('furniture.editor.store') }}" method="POST" id="meterDataForm">
                        @csrf
                        <input type="hidden" name="reading_id" id="reading_id">
                        
                        <div class="form-group">
                            <label>Building Name</label>
                            <input list="buildings_list" name="building" class="form-control" required placeholder="Select or type new building" autocomplete="off">
                            <datalist id="buildings_list">
                                @php
                                    $comp_buildings = config('app_data.buildings', []);
                                    $comp_buildings = array_unique($comp_buildings);
                                    sort($comp_buildings);
                                @endphp
                                @foreach($comp_buildings as $b)
                                    <option value="{{ $b }}">
                                @endforeach
                            </datalist>
                        </div>
                        
                        <div class="form-group">
                            <label>Room</label>
                            <input type="text" name="room" class="form-control" required placeholder="e.g. 101">
                        </div>
                        
                        <div class="form-group">
                            <label>Bed</label>
                            <input type="text" name="bed" class="form-control" placeholder="Qty">
                        </div>
                        
                        <div class="form-group">
                            <label>Table</label>
                            <input type="text" name="table" class="form-control" placeholder="Qty">
                        </div>
                        
                        <div class="form-group">
                            <label>Chair</label>
                            <input type="text" name="chair" class="form-control" placeholder="Qty">
                        </div>

                        <div class="form-group">
                            <label>Cupboard</label>
                            <input type="text" name="cupboard" class="form-control" placeholder="Qty">
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" id="btn-save" formaction="{{ route('furniture.editor.store') }}" style="flex: 1; background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                                Save New
                            </button>
                            
                            <button type="submit" id="btn-update" style="display: none; flex: 1; background: #f59e0b; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
                                Update
                            </button>
                            
                            <button type="submit" id="btn-delete" style="display: none; flex: 1; background: #ef4444; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                Delete
                            </button>

                            <button type="button" id="btn-clear" style="display: none; flex: 1; background: #64748b; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#64748b'">
                                Clear
                            </button>
                        </div>
                    </form>

                </div>
                
                <!-- Table Section -->
                <div style="background: white; border-radius: 20px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                            <span>📖</span> Furniture Records
                        </h3>
                        
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <input type="text" id="fakeSearchBox" readonly placeholder="🔍 Search furniture list..." style="background: #f8fafc; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 200px; cursor: pointer; transition: background 0.2s; margin: 0;" title="Click to search by Building and Room" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                            
                            <form action="{{ route('furniture.editor.import') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                                @csrf
                                <input type="file" name="excel_file" accept=".xlsx,.xls" required style="font-size: 13px; padding: 4px; border: 1px solid #cbd5e1; border-radius: 6px; background: white;">
                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 7px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                    💾 Save Data
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-wrapper">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px 10px; color: #475569;">Building Name</th>
                                    <th style="padding: 12px 10px; color: #475569;">Room</th>
                                    <th style="padding: 12px 10px; color: #475569;">Bed</th>
                                    <th style="padding: 12px 10px; color: #475569;">Table</th>
                                    <th style="padding: 12px 10px; color: #475569;">Chair</th>
                                    <th style="padding: 12px 10px; color: #475569;">Cupboard</th>
                                </tr>
                            </thead>
                            <tbody id="meterTableBody">
                                @forelse($readings as $reading)
                                <tr style="border-bottom: 1px solid #e2e8f0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'" class="data-row" data-id="{{ $reading->id }}">
                                    <td style="padding: 10px;"><strong>{{ $reading->building }}</strong></td>
                                    <td style="padding: 10px;">{{ $reading->room }}</td>
                                    <td style="padding: 10px;">{{ $reading->bed }}</td>
                                    <td style="padding: 10px;">{{ $reading->table }}</td>
                                    <td style="padding: 10px;">{{ $reading->chair }}</td>
                                    <td style="padding: 10px;">{{ $reading->cupboard }}</td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">No furniture records yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="searchModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 25px; width: 25%; min-width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; color: #1e293b; font-weight: 600;">🔍 Search Furniture List</h3>
                <button type="button" id="closeSearchModalBtn" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1;">&times;</button>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 14px;">Building Name</label>
                <input list="modal_buildings_list" id="modalSearchBuilding" placeholder="Select or type building" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;" autocomplete="off">
                <datalist id="modal_buildings_list">
                    @php
                        $comp_buildings = config('app_data.buildings', []);
                        $comp_buildings = array_unique($comp_buildings);
                        sort($comp_buildings);
                    @endphp
                    @foreach($comp_buildings as $b)
                        <option value="{{ $b }}">
                    @endforeach
                </datalist>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 14px;">Room</label>
                <input type="text" id="modalSearchRoom" placeholder="e.g. 101" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>
            
            <button type="button" id="modalSearchBtn" style="width: 100%; background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                Search
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSave = document.getElementById('btn-save');
            const btnUpdate = document.getElementById('btn-update');
            const btnDelete = document.getElementById('btn-delete');
            const btnClear = document.getElementById('btn-clear');
            const hiddenId = document.getElementById('reading_id');

            // Modal logic
            const searchModal = document.getElementById('searchModal');
            const fakeSearchBox = document.getElementById('fakeSearchBox');
            const closeSearchModalBtn = document.getElementById('closeSearchModalBtn');
            const modalSearchBtn = document.getElementById('modalSearchBtn');
            const modalSearchBuilding = document.getElementById('modalSearchBuilding');
            const modalSearchRoom = document.getElementById('modalSearchRoom');

            if (fakeSearchBox) {
                fakeSearchBox.addEventListener('click', function() {
                    searchModal.style.display = 'flex';
                    modalSearchBuilding.focus();
                });
            }

            if (closeSearchModalBtn) {
                closeSearchModalBtn.addEventListener('click', function() {
                    searchModal.style.display = 'none';
                });
            }

            // Also close when clicking outside modal content
            window.addEventListener('click', function(e) {
                if (e.target === searchModal) {
                    searchModal.style.display = 'none';
                }
            });

            if (modalSearchBtn) {
                modalSearchBtn.addEventListener('click', function() {
                    const b = modalSearchBuilding.value.toLowerCase().trim();
                    const r = modalSearchRoom.value.toLowerCase().trim();
                    
                    const rows = document.querySelectorAll('.data-row');
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 2) {
                            const rowB = cells[0].textContent.toLowerCase().trim();
                            const rowR = cells[1].textContent.toLowerCase().trim();
                            
                            const matchB = b === '' || rowB.includes(b);
                            const matchR = r === '' || rowR === r;
                            
                            if (matchB && matchR) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                    
                    searchModal.style.display = 'none';
                });
            }

            function enterEditMode(row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                if (cells.length >= 6 && id) {
                    hiddenId.value = id;
                    document.querySelector('input[name="building"]').value = cells[0].textContent.trim();
                    document.querySelector('input[name="room"]').value = cells[1].textContent.trim();
                    document.querySelector('input[name="bed"]').value = cells[2].textContent.trim();
                    document.querySelector('input[name="table"]').value = cells[3].textContent.trim();
                    document.querySelector('input[name="chair"]').value = cells[4].textContent.trim();
                    document.querySelector('input[name="cupboard"]').value = cells[5].textContent.trim();
                    
                    btnSave.style.display = 'none';
                    btnUpdate.style.display = 'block';
                    btnDelete.style.display = 'block';
                    btnClear.style.display = 'block';
                    
                    // Update form actions
                    document.getElementById('meterDataForm').action = `{{ url('/furniture-editor/update') }}/${id}`;
                    btnUpdate.formAction = `{{ url('/furniture-editor/update') }}/${id}`;
                    btnDelete.formAction = `{{ url('/furniture-editor/delete') }}/${id}`;
                }
            }

            function clearForm() {
                hiddenId.value = '';
                document.getElementById('meterDataForm').reset();
                document.getElementById('meterDataForm').action = "{{ route('furniture.editor.store') }}";
                
                btnSave.style.display = 'block';
                btnUpdate.style.display = 'none';
                btnDelete.style.display = 'none';
                btnClear.style.display = 'none';
            }

            if (btnClear) {
                btnClear.addEventListener('click', clearForm);
            }

            // Add click listeners to rows
            document.querySelectorAll('.data-row').forEach(row => {
                row.addEventListener('click', function() {
                    enterEditMode(this);
                });
            });


        });
    </script>
@endsection

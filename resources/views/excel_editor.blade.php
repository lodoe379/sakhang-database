@extends('layouts.app')

@section('title', 'Meter Readings')

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
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.02em;">Meter Readings Management</h2>
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
                            <span>📋</span> Form of meter
                        </h3>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <input type="text" id="meterSearchInput" placeholder="🔍 Search records..." style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 100%;">
                    </div>
                    
                    <form action="{{ route('excel.editor.store') }}" method="POST" enctype="multipart/form-data" id="meterDataForm">
                        @csrf
                        <input type="hidden" name="reading_id" id="reading_id">
                        
                        <div class="form-group">
                            <label>Building</label>
                            <input type="text" name="building" class="form-control" required placeholder="e.g. Block A">
                        </div>
                        
                        <div class="form-group">
                            <label>Room</label>
                            <input type="text" name="room" class="form-control" required placeholder="e.g. 101">
                        </div>
                        
                        <div class="form-group">
                            <label>Consumer ID</label>
                            <input type="text" name="consumer_id" class="form-control" required placeholder="Enter Consumer ID">
                        </div>
                        
                        <div class="form-group">
                            <label>IN ID</label>
                            <input type="text" name="in_id" class="form-control" required placeholder="Enter IN ID">
                        </div>
                        
                        <div class="form-group">
                            <label>Meter Number</label>
                            <input type="text" name="meter_number" class="form-control" required placeholder="Enter Meter Number">
                        </div>
                        
                        <div class="form-group">
                            <label>Upload Meter Image</label>
                            <input type="file" name="meter_image" class="form-control" accept="image/*">
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" id="btn-save" formaction="{{ route('excel.editor.store') }}" style="flex: 1; background: #3b82f6; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
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
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <h3 style="margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                            <span>📖</span> Meter Date
                        </h3>
                        
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <form action="{{ route('excel.editor.import') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                                @csrf
                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                    📥 Import data.xlsx
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
                                    <th style="padding: 12px 10px; color: #475569;">Consumer ID</th>
                                    <th style="padding: 12px 10px; color: #475569;">IN ID</th>
                                    <th style="padding: 12px 10px; color: #475569;">Meter No.</th>
                                    <th style="padding: 12px 10px; color: #475569;">Image</th>
                                </tr>
                            </thead>
                            <tbody id="meterTableBody">
                                @forelse($readings as $reading)
                                <tr style="border-bottom: 1px solid #e2e8f0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'" class="data-row" data-id="{{ $reading->id }}">
                                    <td style="padding: 10px;"><strong>{{ $reading->building }}</strong></td>
                                    <td style="padding: 10px;">{{ $reading->room }}</td>
                                    <td style="padding: 10px;">{{ $reading->consumer_id }}</td>
                                    <td style="padding: 10px;">{{ $reading->in_id }}</td>
                                    <td style="padding: 10px;">{{ $reading->meter_number }}</td>
                                    <td style="padding: 10px;">
                                        @if($reading->meter_image)
                                            <a href="{{ asset('storage/' . $reading->meter_image) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $reading->meter_image) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                            </a>
                                        @else
                                            <span style="color: #94a3b8; font-size: 11px;">No image</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">No meter readings recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('meterSearchInput');
            const btnSave = document.getElementById('btn-save');
            const btnUpdate = document.getElementById('btn-update');
            const btnDelete = document.getElementById('btn-delete');
            const btnClear = document.getElementById('btn-clear');
            const hiddenId = document.getElementById('reading_id');

            function enterEditMode(row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                if (cells.length >= 5 && id) {
                    hiddenId.value = id;
                    document.querySelector('input[name="building"]').value = cells[0].textContent.trim();
                    document.querySelector('input[name="room"]').value = cells[1].textContent.trim();
                    document.querySelector('input[name="consumer_id"]').value = cells[2].textContent.trim();
                    document.querySelector('input[name="in_id"]').value = cells[3].textContent.trim();
                    document.querySelector('input[name="meter_number"]').value = cells[4].textContent.trim();
                    
                    btnSave.style.display = 'none';
                    btnUpdate.style.display = 'block';
                    btnDelete.style.display = 'block';
                    btnClear.style.display = 'block';

                    btnUpdate.formAction = `/excel-editor/update/${id}`;
                    btnDelete.formAction = `/excel-editor/delete/${id}`;
                }
            }

            if (btnClear) {
                btnClear.addEventListener('click', function() {
                    document.getElementById('meterDataForm').reset();
                    hiddenId.value = '';
                    btnSave.style.display = 'block';
                    btnUpdate.style.display = 'none';
                    btnDelete.style.display = 'none';
                    btnClear.style.display = 'none';
                    if (searchInput) searchInput.value = '';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase().trim();
                    let rows = document.querySelectorAll('#meterTableBody tr:not(.empty-row)');
                    let exactMatchRow = null;
                    
                    rows.forEach(row => {
                        // Look for exact match in Consumer ID, IN ID, or Meter No.
                        if (filter.length > 0) {
                            const cells = row.querySelectorAll('td');
                            if (cells.length >= 5) {
                                const consumerId = cells[2].textContent.trim().toLowerCase();
                                const inId = cells[3].textContent.trim().toLowerCase();
                                const meterNo = cells[4].textContent.trim().toLowerCase();
                                
                                if (consumerId === filter || inId === filter || meterNo === filter) {
                                    exactMatchRow = row;
                                }
                            }
                        }
                    });

                    // Auto-fill the form if an exact match was found
                    if (exactMatchRow) {
                        enterEditMode(exactMatchRow);
                    }
                });
            }

            // Auto-fill form on row click
            document.querySelectorAll('#meterTableBody tr.data-row').forEach(row => {
                row.addEventListener('click', function() {
                    enterEditMode(this);
                    
                    // Optional visual feedback
                    const originalBg = this.style.background;
                    this.style.background = '#e2e8f0';
                    setTimeout(() => {
                        this.style.background = originalBg;
                    }, 300);
                });
            });
        });
    </script>
@endsection

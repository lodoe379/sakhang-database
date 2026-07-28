@extends('layouts.app')

@section('title', 'Furniture Management')

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
    .table-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
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
        @if(session('message'))
            <div id="success-notification"
                style="position: fixed; top: 30px; right: 30px; background: #059669; color: white; padding: 16px 24px; border-radius: 12px; font-weight: 700; z-index: 9999; box-shadow: 0 10px 15px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease-out;">
                <span>✓</span>
                <div>{{ session('message') }}</div>
            </div>
            <script>
                setTimeout(() => {
                    const el = document.getElementById('success-notification');
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    el.style.transition = 'all 0.3s ease-in';
                    setTimeout(() => el.remove(), 300);
                }, 4000);
            </script>
            <style>
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            </style>
        @endif
        <!-- Header Section -->
        <div class="dashboard-header">
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600; letter-spacing: -0.02em;">Furniture Management</h2>
            <div style="display: flex; gap: 8px;">
                <a href="{{ $is_loggedin ? route('dashboard') : route('landing', ['mode' => 'sub-options']) }}" class="btn"
                    style="width: auto; padding: 6px 16px; font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1); color: white; font-weight: 500; transition: all 0.2s; border-radius: 6px; text-decoration: none;"
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'">←
                    {{ $is_loggedin ? 'Dashboard' : 'Back' }}</a>
            </div>
        </div>

        <div class="dashboard-body">


        <!-- Summary & Form Row -->
        <div
            style="display: grid; grid-template-columns: {{ $is_loggedin ? '1fr 2fr' : '1fr' }}; gap: 16px; margin-bottom: 24px; margin-top: 10px;">
            @if($is_loggedin)
                <!-- Left: Summaries (Staff Only) -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <!-- Furniture Lend Box -->
                    <div onclick="showFurnitureTable('Lend')"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 15px; color: white; cursor: pointer; border-radius: 12px; box-shadow: 0 4px 10px rgba(245, 87, 108, 0.2); flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <div style="font-size: 15px; font-weight: 800; text-align: center; margin-bottom: 10px;">🪑 Lend Status
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 5px;">
                            <div style="text-align: center;" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'all')">
                                <div style="font-size: 24px; font-weight: 900;">{{ $total_lend }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">TOTAL</div>
                            </div>
                            <div
                                style="text-align: center; border-left: 1px solid rgba(255,255,255,0.2); border-right: 1px solid rgba(255,255,255,0.2);" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'done')">
                                <div id="count-lend-done" style="font-size: 24px; font-weight: 900;">{{ $lend_done }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">DONE</div>
                            </div>
                            <div style="text-align: center;" onclick="event.stopPropagation(); showFurnitureTable('Lend', 'pending')">
                                <div id="count-lend-pending" style="font-size: 24px; font-weight: 900;">{{ $lend_pending }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">PEND</div>
                            </div>

                        </div>
                    </div>

                    <!-- Furniture Return Box -->
                    <div onclick="showFurnitureTable('Return')"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 15px; color: white; cursor: pointer; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 242, 254, 0.2); flex: 1; display: flex; flex-direction: column; justify-content: center;">
                        <div style="font-size: 15px; font-weight: 800; text-align: center; margin-bottom: 10px;">🔄 Return
                            Status</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 5px;">
                            <div style="text-align: center;" onclick="event.stopPropagation(); showFurnitureTable('Return', 'all')">
                                <div style="font-size: 24px; font-weight: 900;">{{ $total_return }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">TOTAL</div>
                            </div>
                            <div
                                style="text-align: center; border-left: 1px solid rgba(255,255,255,0.2); border-right: 1px solid rgba(255,255,255,0.2);" onclick="event.stopPropagation(); showFurnitureTable('Return', 'done')">
                                <div id="count-return-done" style="font-size: 24px; font-weight: 900;">{{ $return_done }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">DONE</div>
                            </div>
                            <div style="text-align: center;" onclick="event.stopPropagation(); showFurnitureTable('Return', 'pending')">
                                <div id="count-return-pending" style="font-size: 24px; font-weight: 900;">{{ $return_pending }}</div>
                                <div style="font-size: 10px; opacity: 0.8; font-weight: 700;">PEND</div>
                            </div>

                        </div>
                    </div>
                </div>

            @endif

            <!-- Form: Visible to All -->
            <div class="card p-4" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div
                        style="background: #1e3a5f; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                        📝</div>
                    <h5 style="margin: 0; color: #1e3a5f; font-size: 17px; font-weight: 700;">Lend/Return Application Form
                    </h5>
                </div>

                <form action="{{ route('furniture.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div
                        style="display: grid; grid-template-columns: repeat({{ $is_loggedin ? 4 : 2 }}, 1fr); gap: 16px; margin-bottom: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">S.NO
                                (AUTO)</label>
                            <input type="text" id="furniture-sno-input" value="---" disabled
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; background: #f1f5f9; color: #64748b;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">SUBMISSION
                                DATE & TIME</label>
                            <input type="text" value="{{ date('d/m/Y H:i') }}" disabled
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; background: #f1f5f9; color: #64748b;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat({{ $is_loggedin ? 4 : 2 }}, 1fr); gap: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">REQUEST
                                TYPE <span style="color:red;">*</span></label>
                            <select name="type" required onchange="toggleFurnitureFields(this.value)"
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;">
                                <option value="" disabled selected>-- Select --</option>
                                <option value="Lend">Borrow / Lend</option>
                                <option value="Return">Return Item</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">PERSONNEL
                                NAME <span style="color:red;">*</span></label>
                            <input type="text" name="name" required placeholder="Full name"
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">INTERNAL
                                DEPT / OFFICE <span style="color:red;">*</span></label>
                            <input type="text" name="official_name" required placeholder="Designation"
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">CONTACT
                                PHONE <span style="color:red;">*</span></label>
                            <input type="text" name="phone" required placeholder="Active number"
                                style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;">
                        </div>
                    </div>

                    <div id="lend-fields"
                        style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 15px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">EST.
                                RETURN DATE</label>
                            <input type="date" name="return_date"
                                style="width: 100%; padding: 9px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label
                                style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">UPLOAD
                                APPLICATION (PDF/JPG) <span style="color:red;">*</span></label>
                            <input type="file" name="application" accept="image/*,application/pdf"
                                style="width: 100%; padding: 7px 0; border: none; font-size: 12px;">
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 15px; display: block;">DESCRIBE
                            FURNITURE ITEMS & QUANTITIES <span style="color:red;">*</span></label>
                        <div id="furniture-items-container" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="furniture-item-row"
                                style="display: flex; gap: 12px; background: white; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                <input type="text" name="items[0][name]" required placeholder="Item 1 description"
                                    style="flex: 4; border: none; border-bottom: 2px solid #f1f5f9; padding: 6px; font-size: 13px; outline: none;">
                                <input type="number" name="items[0][qty]" required placeholder="Qty"
                                    style="flex: 1; border: none; border-bottom: 2px solid #f1f5f9; padding: 6px; font-size: 13px; outline: none;"
                                    min="1">
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 15px;">
                            <button type="button" onclick="addFurnitureRow()" id="add-item-btn"
                                style="flex: 1; background: #f1f5f9; border: 2px dashed #cbd5e1; color: #64748b; padding: 11px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 12px; transition: all 0.2s;">+
                                Add Another Item</button>
                            <button type="submit" class="btn"
                                style="flex: 2; background: #1e3a5f; color: white; padding: 11px; border-radius: 8px; font-weight: 800; border: none; cursor: pointer; font-size: 13px; letter-spacing: 0.5px;">SUBMIT
                                APPLICATION</button>
                        </div>
                    </div>
                </form>
            <!-- Form end -->
        </div>

        @if($is_loggedin)
            <!-- Furniture Table View -->
            <div id="furniture-table-view" style="display: none; margin-top: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: #1e3a5f; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px;">📋</div>
                        <h4 style="margin: 0; color: #1e3a5f;" id="furniture-table-title">Furniture Records</h4>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button onclick="hideFurnitureTable()" class="btn"
                            style="width: auto; padding: 10px 20px; font-size: 13px; background: #64748b; border: none; color: white; border-radius: 8px; font-weight: 600; cursor: pointer;">Close Table</button>
                    </div>
                </div>

                <div class="card p-4" style="overflow-x: auto;">
                    <table class="table table-bordered w-100" style="font-size: 13px;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th style="width: 50px;">S.No</th>
                                <th style="width: 140px;">Date & Time</th>
                                <th>Personnel</th>
                                <th>Designation</th>
                                <th style="width: 80px;">Type</th>
                                <th style="width: 120px;">Est. Return</th>
                                <th>Items</th>
                                <th style="width: 50px; text-align: center;">Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($furniture as $f)
                                <tr class="furniture-row" data-type="{{ $f->type }}" data-done="{{ $f->done ? '1' : '0' }}">
                                    <td>{{ $f->s_no }}</td>
                                    <td>{{ $f->created_at->format('d/m/y H:i') }}</td>
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
                                    <td style="text-align: center;">
                                        <input type="checkbox" onchange="toggleFurnitureDone({{ $f->id }}, this)" {{ $f->done ? 'checked' : '' }}
                                            style="width: 18px; height: 18px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        const nextLendSno = {{ $nextLendSno }};
        const nextReturnSno = {{ $nextReturnSno }};

        function toggleFurnitureFields(value) {
            const snoInput = document.getElementById('furniture-sno-input');
            if (value === 'Lend') {
                snoInput.value = nextLendSno;
            } else if (value === 'Return') {
                snoInput.value = nextReturnSno;
            } else {
                snoInput.value = '---';
            }

            const lendFields = document.getElementById('lend-fields');
            const applicationInput = document.querySelector('input[name="application"]');
            if (value === 'Lend') {
                lendFields.style.display = 'grid';
                applicationInput.required = true;
            } else {
                lendFields.style.display = 'none';
                applicationInput.required = false;
            }
        }

        let furnitureRowCount = 1;
        function addFurnitureRow() {
            if (furnitureRowCount >= 5) return;

            const container = document.getElementById('furniture-items-container');
            const newRow = document.createElement('div');
            newRow.className = 'furniture-item-row';
            newRow.style.cssText = 'display: flex; gap: 10px; background: white; padding: 8px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 5px;';
            newRow.innerHTML = `
                            <input type="text" name="items[${furnitureRowCount}][name]" required placeholder="Item ${furnitureRowCount + 1}" style="flex: 4; border: none; border-bottom: 1px solid #f1f5f9; padding: 4px; font-size: 12px; outline: none;">
                            <input type="number" name="items[${furnitureRowCount}][qty]" required placeholder="Qty" style="flex: 1; border: none; border-bottom: 1px solid #f1f5f9; padding: 4px; font-size: 12px; outline: none;" min="1">
                        `;
            container.appendChild(newRow);
            furnitureRowCount++;

            if (furnitureRowCount >= 5) {
                const btn = document.getElementById('add-item-btn');
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
                btn.innerText = 'MAX REACHED';
                btn.onclick = null;
            }
        }

        function showFurnitureTable(type = null, statusFilter = 'all') {
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
            let baseTitle = 'Recent Records';
            if (type === 'Lend') baseTitle = 'Lend Records';
            else if (type === 'Return') baseTitle = 'Return Records';

            if (statusFilter === 'done') title.innerText = baseTitle + ' (Done)';
            else if (statusFilter === 'pending') title.innerText = baseTitle + ' (Pending)';
            else title.innerText = baseTitle;

            // Scroll to table
            document.getElementById('furniture-table-view').scrollIntoView({ behavior: 'smooth' });
        }

        function hideFurnitureTable() {
            document.getElementById('furniture-table-view').style.display = 'none';
        }


        function updateFurniture(id, field, value) {
            fetch(`/furniture/update/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ [field]: value })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) alert('Update failed');
                });
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
                    }
                });
        }

    </script>
@endsection
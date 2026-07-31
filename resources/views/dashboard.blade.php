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
    #dashboard-view, #complaint-table-view, #furniture-table-view {
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
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn"
                    style="width: auto; padding: 6px 16px; font-size: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.1); color: white; font-weight: 500; transition: all 0.2s; border-radius: 8px;"
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'">Logout</button>
            </form>
        </div>

        <div class="dashboard-body">


        <!-- Dashboard Content -->
        <div id="dashboard-view">
            <!-- Dashboard Summary Boxes -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 10px;">
                
                @if(session('role') === 'admin' || session('role') === 'staff1')
                <!-- Electrical Complaint Box -->
                <div onclick="window.location.href='{{ route('complaints.index') }}'"
                    style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
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

                @if(session('role') === 'admin' || session('role') === 'staff')

                <!-- Furniture Lend Box -->
                <div onclick="showFurnitureTable('Lend')"
                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
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
                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
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
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
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
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🪑</span> Furniture List
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 46px;">
                        <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">Record Furniture List</div>
                        <div style="font-size: 10px; opacity: 0.7; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">Click to access form</div>
                    </div>
                </div>
                @endif

                <!-- Global Search Box -->
                <div onclick="showGlobalSearch()"
                    style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); padding: 20px; color: white; cursor: pointer; transition: all 0.3s ease; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);"
                    onmouseover="this.style.transform='translateY(-3px)'" 
                    onmouseout="this.style.transform='translateY(0)'">
                    <div style="font-size: 16px; opacity: 0.9; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span>🔍</span> GLOBAL SEARCH
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; height: 46px;">
                        <div style="font-size: 14px; opacity: 0.9; font-weight: 500;">Search Everything</div>
                        <div style="font-size: 10px; opacity: 0.7; font-weight: 700; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">Meter Data & Furniture</div>
                    </div>
                </div>
            </div>

            <!-- (Closed dashboard-view below) -->
        </div>

        <!-- Complaint Table View removed, now in electrical_complaints.blade.php -->


        <!-- Furniture Table View -->
        <div id="furniture-table-view" style="display: none; height: 100%; flex-direction: column;" class="py-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #f093fb; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">🪑</div>
                    <h4 style="margin: 0; font-size: 16px;" id="furniture-table-title">Furniture Records</h4>
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

        <!-- Global Search View -->
        <div id="global-search-view" style="display: none; height: 100%; flex-direction: column;" class="py-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #6366f1; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">🔍</div>
                    <h4 style="margin: 0; font-size: 20px;">Global Search</h4>
                </div>
                <button onclick="showDashboard()" class="btn"
                    style="width: auto; padding: 8px 16px; font-size: 14px; background: #1e3a5f; border: none; color: white; border-radius: 8px; font-weight: 600;">← Back to Dashboard</button>
            </div>

            <div style="margin-bottom: 20px;">
                <input type="text" id="global-search-input" placeholder="Type to search building, room, name, or consumer ID..." 
                    style="width: 100%; padding: 15px 20px; font-size: 16px; border: 2px solid #cbd5e1; border-radius: 12px; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#cbd5e1'">
            </div>

            <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; overflow: hidden;">
                <!-- Meter Data Results -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden;">
                    <div style="background: #f1f5f9; padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 8px;">
                        <span>📋</span> Meter Data Results
                    </div>
                    <div id="search-results-meters" style="flex: 1; overflow-y: auto; padding: 15px;">
                        <div style="color: #94a3b8; text-align: center; margin-top: 20px;">Type above to search...</div>
                    </div>
                </div>

                <!-- Furniture Records Results -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; overflow: hidden;">
                    <div style="background: #f1f5f9; padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 8px;">
                        <span>🪑</span> Furniture Records Results
                    </div>
                    <div id="search-results-furniture" style="flex: 1; overflow-y: auto; padding: 15px;">
                        <div style="color: #94a3b8; text-align: center; margin-top: 20px;">Type above to search...</div>
                    </div>
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
            document.getElementById('global-search-view').style.display = 'none';
        }

        function showGlobalSearch() {
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('furniture-table-view').style.display = 'none';
            document.getElementById('global-search-view').style.display = 'flex';
            document.getElementById('global-search-input').focus();
        }

        // Global Search Logic
        let searchTimeout;
        document.getElementById('global-search-input').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            const meterContainer = document.getElementById('search-results-meters');
            const furnitureContainer = document.getElementById('search-results-furniture');

            if (query.length < 2) {
                meterContainer.innerHTML = '<div style="color: #94a3b8; text-align: center; margin-top: 20px;">Type above to search...</div>';
                furnitureContainer.innerHTML = '<div style="color: #94a3b8; text-align: center; margin-top: 20px;">Type above to search...</div>';
                return;
            }

            meterContainer.innerHTML = '<div style="color: #64748b; text-align: center; margin-top: 20px;">Searching...</div>';
            furnitureContainer.innerHTML = '<div style="color: #64748b; text-align: center; margin-top: 20px;">Searching...</div>';

            searchTimeout = setTimeout(() => {
                fetch(`/dashboard/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        // Render Meters
                        if (data.meters && data.meters.length > 0) {
                            meterContainer.innerHTML = data.meters.map(m => `
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px;">
                                    <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">${m.building} - Room ${m.room}</div>
                                    <div style="font-size: 13px; color: #475569;">Name: ${m.name_on_bill || 'N/A'}</div>
                                    <div style="font-size: 13px; color: #475569;">Meter: ${m.meter_number || 'N/A'} | Consumer ID: ${m.consumer_id || 'N/A'}</div>
                                </div>
                            `).join('');
                        } else {
                            meterContainer.innerHTML = '<div style="color: #ef4444; text-align: center; margin-top: 20px;">No meter records found.</div>';
                        }

                        // Render Furniture
                        if (data.furniture && data.furniture.length > 0) {
                            furnitureContainer.innerHTML = data.furniture.map(f => {
                                let itemsList = '';
                                if (f.items) {
                                    try {
                                        const items = typeof f.items === 'string' ? JSON.parse(f.items) : f.items;
                                        itemsList = items.map(i => `${i.name} (${i.qty})`).join(', ');
                                    } catch(e) {}
                                }
                                return `
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; border-left: 4px solid ${f.type === 'Lend' ? '#ef4444' : '#10b981'}">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <div style="font-weight: 600; color: #0f172a;">${f.name}</div>
                                        <div style="font-size: 11px; font-weight: bold; color: ${f.type === 'Lend' ? '#ef4444' : '#10b981'}">${f.type.toUpperCase()}</div>
                                    </div>
                                    <div style="font-size: 13px; color: #475569;">Official: ${f.official_name || 'N/A'}</div>
                                    <div style="font-size: 13px; color: #475569; margin-top: 4px; font-weight: 500;">Items: ${itemsList}</div>
                                </div>
                                `;
                            }).join('');
                        } else {
                            furnitureContainer.innerHTML = '<div style="color: #ef4444; text-align: center; margin-top: 20px;">No furniture records found.</div>';
                        }
                    });
            }, 300);
        });

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
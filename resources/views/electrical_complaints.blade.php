@extends('layouts.app')

@section('title', 'Electrical Complaints')

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

            <div id="complaint-table-view" style="height: 100%; flex-direction: column;" class="py-2">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">⚡</div>
                        <h4 style="margin: 0; font-size: 16px;" id="complaint-table-title">All Electrical Complaints</h4>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button onclick="document.getElementById('importModal').style.display='flex'" class="btn"
                            style="width: auto; padding: 6px 12px; font-size: 12px; background: #6366f1; border: none; color: white; border-radius: 8px; font-weight: 600;">📤 Import XLSX</button>
                        <a href="{{ route('complaints.export') }}" class="btn"
                            style="width: auto; padding: 6px 12px; font-size: 12px; background: #d97706; border: none; color: white; border-radius: 8px; font-weight: 600; text-decoration: none; display: flex; align-items: center;">📥 Export CSV</a>
                        <button onclick="window.location.reload()" class="btn"
                            style="width: auto; padding: 6px 12px; font-size: 12px; background: #059669; border: none; color: white; border-radius: 8px; font-weight: 600;">↻ Refresh</button>
                    </div>
                </div>

                <div class="card p-2" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                    <div class="table-wrapper">
                        <table class="complaint-table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th style="width: 160px;">Date & Time</th>
                                    <th style="width: 120px;">Action Date</th>
                                    <th>Name & Location</th>
                                    <th style="width: 120px;">Phone</th>
                                    <th>Complaint</th>
                                    <th>User Reply</th>
                                    <th>Remark</th>
                                    <th style="width: 80px;">Files</th>
                                    <th style="width: 60px;">Done</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaints as $c)
                                    <tr class="complaint-row" data-done="{{ $c->done ? '1' : '0' }}">
                                        <td>{{ $c->id }}</td>
                                        <td>{{ $c->created_at }}</td>
                                        <td>
                                            <input type="date" value="{{ $c->action_date }}" 
                                                onchange="updateComplaint({{ $c->id }}, 'action_date', this.value)"
                                                style="padding: 4px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 13px; width: 100%;">
                                        </td>
                                        <td>
                                            <strong>{{ $c->name }}</strong><br>
                                            <span style="font-size: 0.85em; color: #64748b;">{{ $c->building }} - Room {{ $c->room }}</span>
                                        </td>
                                        <td>{{ $c->phone }}</td>
                                        <td>{{ $c->complaint }}</td>
                                        <td style="font-style: italic; color: #475569;">{{ $c->user_reply }}</td>
                                        <td>
                                            <textarea onchange="updateComplaint({{ $c->id }}, 'remark', this.value)"
                                                style="width: 100%; border: 1px solid #e2e8f0; border-radius: 4px; padding: 4px; font-size: 13px; min-height: 40px;">{{ $c->remark }}</textarea>
                                        </td>
                                        <td>
                                            @if($c->image) 
                                                <a href="{{ asset('storage/' . $c->image) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $c->image) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                                </a> 
                                            @endif
                                            @if($c->signature)
                                                <div style="margin-top: 5px; border-top: 1px solid #eee; padding-top: 5px;">
                                                    <img src="{{ $c->signature }}" style="width: 80px; height: auto; max-height: 40px; background: #fff; border-radius: 4px;">
                                                    <div style="font-size: 9px; color: #94a3b8; font-weight: 700; text-transform: uppercase;">E-Sign</div>
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" onchange="toggleDone({{ $c->id }}, this)" {{ $c->done ? 'checked' : '' }}
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
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: #1e293b; font-size: 18px;">Import Electrical Complaints</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Upload an XLSX file containing complaints data. Ensure columns match the standard export format.</p>
            
            <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Select File (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx" required
                        style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                        style="padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; font-weight: 600; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 10px 20px; border-radius: 8px; border: none; background: #3b82f6; color: white; font-weight: 600; cursor: pointer;">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleDone(id, checkbox) {
            const isChecked = checkbox.checked;
            fetch(`/complaint/toggle/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    checkbox.checked = !isChecked;
                    alert(data.error || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkbox.checked = !isChecked;
            });
        }

        function updateComplaint(id, field, value) {
            fetch(`/complaint/update/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ [field]: value })
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

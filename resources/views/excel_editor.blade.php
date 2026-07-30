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

                    
                    <form action="{{ route('excel.editor.store') }}" method="POST" enctype="multipart/form-data" id="meterDataForm">
                        @csrf
                        <input type="hidden" name="reading_id" id="reading_id">
                        
                        <div class="form-group">
                            <label>Building Name</label>
                            <input type="text" name="building" class="form-control" required placeholder="e.g. Block A">
                        </div>
                        
                        <div class="form-group">
                            <label>Room</label>
                            <input type="text" name="room" class="form-control" required placeholder="e.g. 101">
                        </div>

                        <div class="form-group">
                            <label>Name on the bill</label>
                            <input type="text" name="name_on_bill" class="form-control" placeholder="Enter Name on the bill">
                        </div>
                        
                        <div class="form-group">
                            <label>IN ID</label>
                            <input type="text" name="in_id" class="form-control" placeholder="Enter IN ID">
                        </div>
                        
                        <div class="form-group">
                            <label>Meter Number</label>
                            <input type="text" name="meter_number" class="form-control" placeholder="Enter Meter Number">
                        </div>
                        
                        <div class="form-group">
                            <label>Consumer ID</label>
                            <input type="text" name="consumer_id" class="form-control" placeholder="Enter Consumer ID">
                        </div>

                        <div class="form-group">
                            <label>Account No</label>
                            <input type="text" name="account_no" class="form-control" placeholder="Enter Account No">
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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                            <span>📖</span> Meter Data
                        </h3>
                        
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <input type="text" id="fakeSearchBox" readonly placeholder="🔍 Advanced Search..." style="background: #f8fafc; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 200px; cursor: pointer; transition: background 0.2s; margin: 0;" title="Click to search by Building and Room" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                            
                            <form action="{{ route('excel.editor.import') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                                @csrf
                                <label for="excel-upload" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: background 0.2s; margin: 0;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                                    📄 Upload Data
                                </label>
                                <input type="file" id="excel-upload" name="excel_file" accept=".xlsx,.xls" required style="display: none;" onchange="if(this.files[0]) { this.form.submit(); }">
                            </form>
                        </div>
                    </div>
                    

                    

                    
                    <div class="table-wrapper">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 12px 10px; color: #475569;">Building Name</th>
                                    <th style="padding: 12px 10px; color: #475569;">Room</th>
                                    <th style="padding: 12px 10px; color: #475569;">Name on the bill</th>
                                    <th style="padding: 12px 10px; color: #475569;">IN ID</th>
                                    <th style="padding: 12px 10px; color: #475569;">Meter Number</th>
                                    <th style="padding: 12px 10px; color: #475569;">Consumer ID</th>
                                    <th style="padding: 12px 10px; color: #475569;">Account No</th>
                                    <th style="padding: 12px 10px; color: #475569;">Image</th>
                                </tr>
                            </thead>
                            <tbody id="meterTableBody">
                                @forelse($readings as $reading)
                                <tr style="border-bottom: 1px solid #e2e8f0; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'" class="data-row" data-id="{{ $reading->id }}">
                                    <td style="padding: 10px;"><strong>{{ $reading->building }}</strong></td>
                                    <td style="padding: 10px;">{{ $reading->room }}</td>
                                    <td style="padding: 10px;">{{ $reading->name_on_bill }}</td>
                                    <td style="padding: 10px;">{{ $reading->in_id }}</td>
                                    <td style="padding: 10px;">{{ $reading->meter_number }}</td>
                                    <td style="padding: 10px;">{{ $reading->consumer_id }}</td>
                                    <td style="padding: 10px;">{{ $reading->account_no }}</td>
                                    <td style="padding: 10px;">
                                        @if($reading->meter_image)
                                            <img src="{{ asset('storage/' . $reading->meter_image) }}" class="meter-thumbnail" data-url="{{ asset('storage/' . $reading->meter_image) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0; cursor: pointer;">
                                        @else
                                            <span style="color: #94a3b8; font-size: 11px;">No image</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="8" style="padding: 20px; text-align: center; color: #64748b;">No meter readings recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Search Modal (1/4 page size) -->
    <div id="searchModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 25px; width: 25%; min-width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 18px; color: #1e293b; font-weight: 600;">🔍 Advanced Search</h3>
                <button type="button" id="closeSearchModalBtn" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1;">&times;</button>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 14px;">Building Name</label>
                <select id="modalSearchBuilding" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    <option value="">-- All Buildings --</option>
                    @php
                        $uniqueBuildings = collect();
                        $buildingRooms = [];

                        foreach($readings as $r) {
                            $b = ucwords(strtolower(trim($r->building)));
                            $room = trim($r->room);
                            
                            if ($b === '') continue;
                            
                            if (!$uniqueBuildings->contains($b)) {
                                $uniqueBuildings->push($b);
                            }
                            
                            if (!isset($buildingRooms[$b])) {
                                $buildingRooms[$b] = [];
                            }
                            
                            if ($room !== '' && !in_array($room, $buildingRooms[$b])) {
                                $buildingRooms[$b][] = $room;
                            }
                        }

                        $uniqueBuildings = $uniqueBuildings->sort()->values();
                        
                        foreach($buildingRooms as $b => $rooms) {
                            usort($buildingRooms[$b], 'strnatcasecmp');
                        }
                    @endphp
                    @foreach($uniqueBuildings as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1e293b; font-size: 14px;">Room</label>
                <select id="modalSearchRoom" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    <option value="">-- All Rooms --</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="button" id="modalSearchSubmitBtn" style="flex: 1; background: #3b82f6; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">Search</button>
                <button type="button" id="modalSearchClearBtn" style="flex: 1; background: #64748b; color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#64748b'">Clear & Close</button>
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: transparent; z-index: 10000; justify-content: center; align-items: center; overflow: hidden;">
        
        <!-- Zoom Controls -->
        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10001;">
            <button type="button" onclick="zoomImage(event, 0.2)" style="background: rgba(0,0,0,0.6); border: 2px solid #333; color: white; width: 50px; height: 50px; border-radius: 25px; font-size: 24px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">+</button>
            <button type="button" onclick="zoomImage(event, -0.2)" style="background: rgba(0,0,0,0.6); border: 2px solid #333; color: white; width: 50px; height: 50px; border-radius: 25px; font-size: 24px; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">&minus;</button>
        </div>

        <button type="button" onclick="closeImageModal()" style="position: absolute; top: 20px; right: 30px; background: rgba(0,0,0,0.6); border: none; color: white; font-size: 30px; width: 40px; height: 40px; border-radius: 20px; display: flex; justify-content: center; align-items: center; cursor: pointer; z-index: 10001; line-height: 1; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">&times;</button>
        
        <img id="modalImage" src="" draggable="false" style="width: 30vw; max-height: 85vh; height: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); cursor: grab; transition: transform 0.1s ease-out; transform-origin: center center;">
    </div>

    <script>
        let imgScale = 1;
        let imgIsDragging = false;
        let imgStartX, imgStartY;
        let imgTranslateX = 0, imgTranslateY = 0;

        let imgModalElement = null;
        let imgModalImage = null;

        function openImageModal(e, url) {
            if (e) {
                e.preventDefault();
            }
            imgModalImage.src = url;
            // Reset transforms
            imgScale = 1;
            imgTranslateX = 0;
            imgTranslateY = 0;
            updateImageTransform();
            imgModalElement.style.display = 'flex';
        }

        function closeImageModal() {
            imgModalElement.style.display = 'none';
        }

        function updateImageTransform() {
            imgModalImage.style.transform = `translate(${imgTranslateX}px, ${imgTranslateY}px) scale(${imgScale})`;
        }

        function zoomImage(e, amount) {
            if (e && e.stopPropagation) e.stopPropagation();
            imgScale += amount;
            if (imgScale < 0.2) imgScale = 0.2;
            updateImageTransform();
        }

        function startDragging(x, y) {
            imgIsDragging = true;
            imgModalImage.style.cursor = 'grabbing';
            imgModalImage.style.transition = 'none';
            imgStartX = x - imgTranslateX;
            imgStartY = y - imgTranslateY;
        }

        function moveDragging(x, y) {
            imgTranslateX = x - imgStartX;
            imgTranslateY = y - imgStartY;
            updateImageTransform();
        }

        function stopDragging() {
            if (imgIsDragging) {
                imgIsDragging = false;
                imgModalImage.style.cursor = 'grab';
                imgModalImage.style.transition = 'transform 0.1s ease-out';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            imgModalElement = document.getElementById('imageModal');
            imgModalImage = document.getElementById('modalImage');

            // Close when clicking outside the image
            if (imgModalElement) {
                imgModalElement.addEventListener('click', function(e) {
                    if (e.target === imgModalElement) {
                        closeImageModal();
                    }
                });

                // Zoom logic (Wheel)
                imgModalElement.addEventListener('wheel', function(e) {
                    e.preventDefault();
                    zoomImage(e, e.deltaY < 0 ? 0.15 : -0.15);
                }, { passive: false });
            }

            if (imgModalImage) {
                // Image Drag logic (Mouse)
                imgModalImage.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    startDragging(e.clientX, e.clientY);
                });

                // Image Drag logic (Touch)
                imgModalImage.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 1) {
                        if (e.cancelable) e.preventDefault(); 
                        startDragging(e.touches[0].clientX, e.touches[0].clientY);
                    }
                }, { passive: false });
            }

            window.addEventListener('mousemove', function(e) {
                if (!imgIsDragging) return;
                e.preventDefault();
                moveDragging(e.clientX, e.clientY);
            });

            window.addEventListener('mouseup', stopDragging);

            window.addEventListener('touchmove', function(e) {
                if (imgIsDragging && e.touches.length === 1) {
                    if (e.cancelable) e.preventDefault();
                    moveDragging(e.touches[0].clientX, e.touches[0].clientY);
                }
            }, { passive: false });

            window.addEventListener('touchend', stopDragging);
            const btnSave = document.getElementById('btn-save');
            const btnUpdate = document.getElementById('btn-update');
            const btnDelete = document.getElementById('btn-delete');
            const btnClear = document.getElementById('btn-clear');
            const hiddenId = document.getElementById('reading_id');

            // Modal elements
            const fakeSearchBox = document.getElementById('fakeSearchBox');
            const searchModal = document.getElementById('searchModal');
            const closeSearchModalBtn = document.getElementById('closeSearchModalBtn');
            const modalSearchSubmitBtn = document.getElementById('modalSearchSubmitBtn');
            const modalSearchClearBtn = document.getElementById('modalSearchClearBtn');
            const modalSearchBuilding = document.getElementById('modalSearchBuilding');
            const modalSearchRoom = document.getElementById('modalSearchRoom');

            function enterEditMode(row) {
                const id = row.getAttribute('data-id');
                const cells = row.querySelectorAll('td');
                
                if (cells.length >= 7 && id) {
                    hiddenId.value = id;
                    document.querySelector('[name="building"]').value = cells[0].textContent.trim();
                    document.querySelector('[name="room"]').value = cells[1].textContent.trim();
                    document.querySelector('input[name="name_on_bill"]').value = cells[2].textContent.trim();
                    document.querySelector('input[name="in_id"]').value = cells[3].textContent.trim();
                    document.querySelector('input[name="meter_number"]').value = cells[4].textContent.trim();
                    document.querySelector('input[name="consumer_id"]').value = cells[5].textContent.trim();
                    document.querySelector('input[name="account_no"]').value = cells[6].textContent.trim();
                    
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
                });
            }

            // Click listener for image thumbnails
            document.querySelectorAll('.meter-thumbnail').forEach(img => {
                img.addEventListener('click', function(e) {
                    // Stop propagation so clicking the image DOES NOT select the row!
                    e.stopPropagation();
                    openImageModal(e, this.getAttribute('data-url'));
                });
            });

            // Modal Event Listeners
            const buildingRoomsData = @json($buildingRooms);

            if (modalSearchBuilding && modalSearchRoom) {
                modalSearchBuilding.addEventListener('change', function() {
                    const selectedBuilding = this.value;
                    modalSearchRoom.innerHTML = '<option value="">-- All Rooms --</option>';
                    if (selectedBuilding && buildingRoomsData[selectedBuilding]) {
                        buildingRoomsData[selectedBuilding].forEach(room => {
                            const option = document.createElement('option');
                            option.value = room;
                            option.textContent = room;
                            modalSearchRoom.appendChild(option);
                        });
                    }
                });
            }

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

            window.addEventListener('click', function(event) {
                if (event.target == searchModal) {
                    searchModal.style.display = 'none';
                }
            });

            if (modalSearchSubmitBtn) {
                // Allow pressing Enter in text fields to search
                const triggerSearch = function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        modalSearchSubmitBtn.click();
                    }
                };
                modalSearchBuilding.addEventListener('keydown', triggerSearch);
                modalSearchRoom.addEventListener('keydown', triggerSearch);

                modalSearchSubmitBtn.addEventListener('click', function() {
                    let bFilter = modalSearchBuilding.value.toLowerCase().trim();
                    let rFilter = modalSearchRoom.value.toLowerCase().trim();
                    
                    let rows = document.querySelectorAll('#meterTableBody tr:not(.empty-row)');
                    let exactMatchRow = null;
                    let matchedRows = [];
                    
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 7) {
                            const building = cells[0].textContent.trim().toLowerCase();
                            const room = cells[1].textContent.trim().toLowerCase();
                            
                            let bMatch = bFilter === '' || building.includes(bFilter);
                            let rMatch = rFilter === '' || room.includes(rFilter);
                            
                            if (bMatch && rMatch) {
                                matchedRows.push(row);
                                
                                // Check for exact matches
                                if (bFilter !== '' && rFilter !== '' && building === bFilter && room === rFilter) {
                                    exactMatchRow = row;
                                } else if (bFilter !== '' && rFilter === '' && building === bFilter) {
                                    exactMatchRow = row;
                                } else if (rFilter !== '' && bFilter === '' && room === rFilter) {
                                    exactMatchRow = row;
                                }
                            }
                        }
                    });

                    // If we found a perfect exact match, use it. 
                    // Otherwise, if there is exactly ONE row that partially matches, use that row.
                    if (exactMatchRow) {
                        enterEditMode(exactMatchRow);
                    } else if (matchedRows.length === 1) {
                        enterEditMode(matchedRows[0]);
                    }
                    
                    searchModal.style.display = 'none';
                });
            }

            if (modalSearchClearBtn) {
                modalSearchClearBtn.addEventListener('click', function() {
                    modalSearchBuilding.value = '';
                    modalSearchRoom.value = '';
                    
                    searchModal.style.display = 'none';
                });
            }

            document.querySelectorAll('#meterTableBody tr.data-row').forEach(row => {
                row.addEventListener('click', function() {
                    enterEditMode(this);
                    
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

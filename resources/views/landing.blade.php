@extends('layouts.app')

@section('content')
    <div class="landing-wrapper bg-animated-mesh" id="landing-main-wrapper">

        <!-- RIGHT PANEL: CONTENT -->
        <div class="right-panel" id="primary-panel" style="background: transparent;">
            @if(session('message'))
                <div id="success-notification" class="glass-panel" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 40px; border-radius: 30px; font-weight: 700; z-index: 9999; display: flex; flex-direction: column; align-items: center; gap: 20px; animation: modalPop 0.5s cubic-bezier(0.16, 1, 0.3, 1); min-width: 350px; text-align: center;">
                    <div style="background: #22c55e; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);">✓</div>
                    <div style="font-size: 22px; letter-spacing: 0.5px;">{{ session('message') }}</div>
                    
                    @if(session('show_emergency'))
                        <div style="margin-top: 10px; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 20px; width: 100%;">
                            <p style="font-size: 14px; color: #94a3b8; margin-bottom: 15px; letter-spacing: 1px;"><span style="text-transform: uppercase;">Maintenance Contacts</span> : “Power out? Call us.”</p>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 12px 20px; border-radius: 12px;">
                                    <span style="color: #fbce04; font-weight: 800;">Tenzin Lodoe</span>
                                    <span style="font-family: monospace; font-size: 16px;">7505599379</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 12px 20px; border-radius: 12px;">
                                    <span style="color: #fbce04; font-weight: 800;">Norbu Wangyal</span>
                                    <span style="font-family: monospace; font-size: 16px;">9882134069</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button onclick="this.parentElement.remove()" style="margin-top: 10px; background: #fbce04; color: #0f172a; border: none; padding: 15px 40px; border-radius: 50px; font-weight: 800; cursor: pointer; transition: all 0.2s;">DISMISS</button>
                </div>
                <style>
                    @keyframes modalPop {
                        from { transform: translate(-50%, -40%) scale(0.9); opacity: 0; }
                        to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
                    }
                </style>
            @endif

            <div class="login-card portal-mode glass-panel animate-fade-in" id="main-content-card">
                
                <!-- MODE SELECTION / PORTAL -->
                <div id="login-section" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px;">
                    <div class="mode-container" id="main-minimal-menu" style="gap: 40px; width: 100%; justify-content: center;">
                        <div class="mode-box sakhang-main glass-card" onclick="togglePortalOptions()" style="flex: 0 1 300px; padding: 60px 40px; border-radius: 40px;">
                            <div class="icon" style="color: #10b981; font-size: 64px; margin-bottom: 20px;">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor"><path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z" /></svg>
                            </div>
                            <div class="label" style="font-size: 24px; letter-spacing: 1px;">Sakhang</div>
                        </div>
                        <div class="mode-box track-main glass-card" onclick="selectMode('status')" style="flex: 0 1 300px; padding: 60px 40px; border-radius: 40px;">
                            <div class="icon" style="color: #6366f1; font-size: 64px; margin-bottom: 20px;">📊</div>
                            <div class="label" style="font-size: 24px; letter-spacing: 1px;">Track Progress</div>
                        </div>
                    </div>

                    <div id="portal-sub-options" style="display:none; animation: fadeIn 0.4s ease-out; width: 100%;">
                        <div class="mode-container" style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <div class="mode-box electrical glass-card" onclick="selectMode('complaint')">
                                <div class="icon">⚡</div>
                                <div class="label">Electrical Complaint</div>
                            </div>
                            <div class="mode-box furniture glass-card" onclick="selectMode('furniture-options')">
                                <div class="icon">🪑</div>
                                <div class="label">Furniture Services</div>
                            </div>
                            <div class="mode-box room-furniture glass-card" onclick="selectMode('room-furniture')">
                                <div class="icon">📋</div>
                                <div class="label">Room Furniture List</div>
                            </div>
                            <div class="mode-box consumer glass-card" onclick="selectMode('consumer')">
                                <div class="icon">🔍</div>
                                <div class="label">Consumer Search</div>
                            </div>
                        </div>
                        <button type="button" onclick="resetToMinimal()" style="margin-top:40px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 15px; border-radius: 50px; cursor: pointer; font-weight: 700; width: 100%; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">← Back to Menu</button>
                    </div>

                    <!-- Subtle Admin Access Link inside login-section so it only shows on home page -->
                    <div id="admin-login-btn-container" style="margin-top: 40px; opacity: 0.35; transition: all 0.3s ease; text-align: center;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.35'">
                        <button onclick="selectMode('admin')" style="background: rgba(30, 58, 95, 0.5); color: white; border: 1px solid rgba(255,255,255,0.1); padding: 10px 25px; border-radius: 50px; cursor: pointer; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">ADMIN LOGIN</button>
                    </div>

                </div>

                <!-- ADMINISTRATOR LOGIN FORM -->
                <div id="admin-login-form" style="display:none; flex-direction: column; align-items: center; justify-content: center; width: 100%; min-height: 400px; animation: fadeIn 0.5s ease;">
                    <div style="width: 100%; max-width: 450px;">
                        <div class="login-header" id="auth-header" style="border-left: 6px solid #3b82f6; padding-left: 20px; margin-bottom: 40px;">
                            <h2 class="form-title" style="color: #3b82f6 !important;">Administrator Login</h2>
                            <p style="color: #94a3b8;">Enter your master credentials to access the system dashboard.</p>
                        </div>

                        @if(session('error'))
                            <div id="login-error" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px; animation: shake 0.5s both;">
                                <span>⚠️</span> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Official ID</label>
                                <input type="text" name="id" id="login-id-field" required placeholder="Enter Admin ID">
                            </div>
                            <div class="form-group">
                                <label>Master Password</label>
                                <input type="password" name="password" required placeholder="••••••••">
                            </div>
                            <button type="submit" class="btn btn-sign-in" style="background: #3b82f6; color: white; border-radius: 50px; padding: 18px; font-weight: 800; margin-top: 10px; border: none; width: 100%; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(59,130,246,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">AUTHORIZE ADMIN</button>
                            <div style="text-align: center; margin-top: 35px;">
                                <button type="button" onclick="selectMode('')" style="background: transparent; border: none; color: #94a3b8; font-size: 14px; font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s;" onmouseover="this.style.color='#ffffff'; this.style.transform='translateX(-5px)'" onmouseout="this.style.color='#94a3b8'; this.style.transform='translateX(0)'">← Back</button>
                            </div>
                        </form>
                    </div>
                </div>





                <div id="public-complaint-form" style="display:none;">
                    <div class="login-header" style="border-left: 6px solid #fbce04; padding-left: 20px;">
                        <h2 class="form-title" style="color: #fbce04 !important; display: flex; align-items: center; gap: 10px;">
                            <span>⚡</span> Electrical Complaint
                        </h2>
                        <p>Report an electrical issue for prompt maintenance.</p>
                    </div>
                    <form action="{{ route('complaint.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="landscape-form-grid" style="grid-template-columns: 1fr 1fr; gap: 30px;">
                            <div class="form-group"><label>Reference S.No</label><input type="text" value="{{ $nextComplaintSno }}" disabled></div>
                            <div class="form-group"><label>Submission Date</label><input type="text" value="{{ date('d M Y, H:i') }}" disabled></div>
                            
                            <div class="form-group"><label>Full Name <span style="color:red;">*</span></label><input type="text" name="name" required placeholder="Your registered name"></div>
                            <div class="form-group"><label>Contact Phone <span style="color:red;">*</span></label><input type="text" name="phone" required placeholder="Active phone number"></div>
                            
                            <div class="form-group">
                                <label>Building Name <span style="color:red;">*</span></label>
                                <select name="building" required>
                                    <option value="" disabled selected>-- Select Building --</option>
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
                            <div class="form-group"><label>Room Number <span style="color:red;">*</span></label><input type="text" name="room" required placeholder="e.g. 101"></div>
                            
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Complaint Details <span style="color:red;">*</span></label>
                                <textarea name="complaint" rows="3" required placeholder="Describe the issue in detail..."></textarea>
                            </div>

                            <div class="form-group"><label>Attach Photo <span style="color:red;">*</span></label><input type="file" name="image" required accept="image/*" style="border:none; padding:10px 0;"></div>
                            <div class="form-group"><label>Attach Video</label><input type="file" name="video" accept="video/*" style="border:none; padding:10px 0;"></div>
                        </div>
                        <button type="submit" class="btn btn-sign-in" style="background: #1e3a5f; color: white; border-radius: 50px; padding: 18px; font-weight: 700; margin-top: 20px;">SUBMIT</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" onclick="selectMode('sub-options')" style="background: transparent; border: none; color: #64748b; font-size: 18px; font-weight: 800; cursor: pointer; text-decoration: none;">Back</button>
                        </div>
                    </form>
                </div>


                <div id="public-furniture-form" style="display:none;">
                    <div class="login-header" style="border-left: 6px solid #f093fb; padding-left: 20px;">
                        <h2 class="form-title" style="color: #f093fb !important; display: flex; align-items: center; gap: 10px;">
                            <span>🪑</span> Furniture Services
                        </h2>
                        <p>Apply for furniture lending or return existing items.</p>
                    </div>
                    <form action="{{ route('furniture.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="landscape-form-grid" style="grid-template-columns: 1fr 1fr; gap: 30px;">
                            <div class="form-group">
                                <label>Reference S.No (AUTO)</label>
                                <input type="text" id="furniture-sno-input" value="---" disabled>
                            </div>
                            <div class="form-group">
                                <label>Submission Date & Time</label>
                                <input type="text" value="{{ date('d/m/Y H:i') }}" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Request Type <span style="color:red;">*</span></label>
                                <select name="type" required onchange="toggleFurnitureFields(this.value)">
                                    <option value="" disabled selected>-- Select --</option>
                                    <option value="Lend">Borrow / Lend</option>
                                    <option value="Return">Return Item</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Personal Name <span style="color:red;">*</span></label>
                                <input type="text" name="name" required placeholder="Full name">
                            </div>
                            
                            <div class="form-group">
                                <label>Internal Dept / Office <span style="color:red;">*</span></label>
                                <input type="text" name="official_name" required placeholder="Designation">
                            </div>
                            <div class="form-group">
                                <label>Contact Phone <span style="color:red;">*</span></label>
                                <input type="text" name="phone" required placeholder="Active number">
                            </div>
                        </div>

                        <div id="lend-fields" style="display: none; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 15px;">
                            <div class="form-group">
                                <label>Est. Return Date</label>
                                <input type="date" name="return_date">
                            </div>
                            <div class="form-group">
                                <label>Upload Application (PDF/JPG) <span style="color:red;">*</span></label>
                                <input type="file" name="application" accept="image/*,application/pdf" style="border:none; padding:10px 0;">
                            </div>
                        </div>

                        <div style="margin-top: 20px;">
                            <label style="font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 15px; display: block;">DESCRIBE FURNITURE ITEMS & QUANTITIES <span style="color:red;">*</span></label>
                            <div id="furniture-items-container" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="furniture-item-row" style="display: flex; gap: 12px; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.1);">
                                    <input type="text" name="items[0][name]" required placeholder="Item 1 description" style="flex: 4; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 14px;">
                                    <input type="number" name="items[0][qty]" required placeholder="Qty" style="flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 14px;" min="1">
                                </div>
                            </div>
                            <button type="button" onclick="addFurnitureRow()" id="add-item-btn" style="margin-top: 15px; background: transparent; border: 2px dashed #cbd5e1; color: #64748b; padding: 11px; border-radius: 8px; cursor: pointer; font-weight: 700; width: 100%;">+ Add Another Item</button>
                        </div>
                        
                        <button type="submit" class="btn btn-sign-in" style="background: #f093fb; color: white; border-radius: 50px; padding: 18px; font-weight: 700; margin-top: 20px; width: 100%;">SUBMIT APPLICATION</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" onclick="selectMode('sub-options')" style="background: transparent; border: none; color: #64748b; font-size: 18px; font-weight: 800; cursor: pointer; text-decoration: none;">Back</button>
                        </div>
                    </form>
                </div>


                <!-- CONSUMER SEARCH FORM -->
                <div id="public-consumer-form" style="display:none;">
                    <div class="login-header">
                        <h2 class="form-title">Search Consumer ID</h2>
                        <p>Locate your consumer records by building and room.</p>
                    </div>
                    <form action="{{ route('consumer.search') }}" method="GET">
                        <div class="form-group">
                            <label>Building Name</label>
                            <select name="building" required>
                                <option value="" disabled {{ !isset($search_building) ? 'selected' : '' }}>-- Select Building --</option>
                                @php
                                    $buildings = config('app_data.buildings', []);
                                    $buildings = array_unique($buildings);
                                    sort($buildings);
                                @endphp
                                @foreach($buildings as $b)
                                    <option value="{{ $b }}" {{ (isset($search_building) && $search_building == $b) ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Room No / Official Name</label>
                            <input type="text" name="room" required placeholder="Enter search criteria" value="{{ $search_room ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-sign-in" style="background: #1e3a5f; color: white; border-radius: 50px; padding: 18px; font-weight: 700;">SEARCH RECORDS</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" onclick="selectMode('sub-options')" style="background: transparent; border: none; color: #64748b; font-size: 18px; font-weight: 800; cursor: pointer; text-decoration: none;">Back</button>
                        </div>
                    </form>
                </div>

                <!-- STATUS CHECK FORM -->
                <div id="public-status-form" style="display:none;">
                    <div class="login-header">
                        <h2 class="form-title">Electrical Complaint Status</h2>
                        <p>Track the progress of your maintenance requests.</p>
                    </div>
                    <form action="{{ route('complaint.status') }}" method="GET">
                        <div class="landscape-form-grid" style="grid-template-columns: 1fr 1fr; gap: 30px;">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="phone" required placeholder="Registered phone no" value="{{ $search_phone ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label>Search Year</label>
                                <select name="year">
                                    @php $currentYear = date('Y'); @endphp
                                    @for($y = $currentYear; $y >= 2024; $y--)
                                        <option value="{{ $y }}" {{ (isset($search_year) && $search_year == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sign-in" style="background: #1e3a5f; color: white; border-radius: 50px; padding: 18px; font-weight: 700; margin-top: 20px;">TRACK STATUS</button>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" onclick="showLogin()" style="background: transparent; border: none; color: #64748b; font-size: 18px; font-weight: 800; cursor: pointer; text-decoration: none;">Back</button>
                        </div>
                    </form>
                </div>

                <!-- RESULTS SECTION -->
                <div id="search-results">
                    <!-- RESULTS DISPLAY -->
                    @if(isset($consumers) && count($consumers) > 0)
                    <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 30px;">
                        <h4 style="color: #1e293b; margin-bottom: 25px; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                            <span style="background: #3b82f6; width: 8px; height: 24px; border-radius: 4px;"></span>
                            Consumer Records Found ({{ count($consumers) }})
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                            @foreach($consumers as $consumer)
                                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03); position: relative; overflow: hidden; display: flex; flex-direction: column;">
                                    <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #84cc16;"></div>
                                    
                                    <div style="margin-bottom: 20px;">
                                        <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Consumer ID (CID)</div>
                                        <div style="font-size: 2.2rem; font-weight: 900; color: #0f172a; letter-spacing: -1px; line-height: 1;">{{ $consumer['cid'] ?? 'N/A' }}</div>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; border-top: 1px solid #f1f5f9; margin-top: 10px; padding-top: 20px;">
                                        <div style="grid-column: span 2; margin-bottom: 5px;">
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Registered Name</div>
                                            <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;">{{ $consumer['name'] ?? 'N/A' }}</div>
                                        </div>

                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Building Name</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $consumer['building'] ?? 'N/A' }}</div>
                                        </div>

                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Room / No</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $consumer['room'] ?? 'N/A' }}</div>
                                        </div>
                                        
                                        @if(!empty($consumer['account']))
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Account No</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['account'] }}</div>
                                        </div>
                                        @endif
                                        
                                        @if(!empty($consumer['installation']))
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Installation</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['installation'] }}</div>
                                        </div>
                                        @endif

                                        @if(!empty($consumer['meter']))
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Meter No</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['meter'] }}</div>
                                        </div>
                                        @endif

                                        @if(!empty($consumer['inid']))
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">INID</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['inid'] }}</div>
                                        </div>
                                        @endif

                                        @if(!empty($consumer['phase']))
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Phase</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['phase'] }}</div>
                                        </div>
                                        @endif

                                        @if(!empty($consumer['department']))
                                        <div style="grid-column: span 2;">
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Department</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #475569;">{{ $consumer['department'] }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(isset($consumers))
                    <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 60px; text-align: center;">
                        <h4 style="color: #1e293b; font-weight: 800; font-size: 1.5rem;">No Records Found</h4>
                        <p style="color: #64748b; margin-top: 10px;">We couldn't find any consumer matches. Try another search.</p>
                        <a href="{{ route('landing', ['mode' => 'consumer']) }}" class="btn" style="display: inline-block; background: #1e3a5f; color: white; border-radius: 50px; margin-top: 30px; width: auto; padding: 14px 40px; font-weight: 700; text-decoration: none;">RETRY SEARCH</a>
                    </div>
                @endif
                
                @if(isset($furnitures) && count($furnitures) > 0)
                    <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 30px;">
                        <h4 style="color: #1e293b; margin-bottom: 25px; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                            <span style="background: #f59e0b; width: 8px; height: 24px; border-radius: 4px;"></span>
                            Furniture Records Found ({{ count($furnitures) }})
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                            @foreach($furnitures as $f)
                                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03); position: relative; overflow: hidden; display: flex; flex-direction: column;">
                                    <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #f59e0b;"></div>
                                    
                                    <div style="margin-bottom: 20px;">
                                        <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Building & Room</div>
                                        <div style="font-size: 1.8rem; font-weight: 900; color: #0f172a; letter-spacing: -1px; line-height: 1.2;">{{ $f->building }} - {{ $f->room }}</div>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; border-top: 1px solid #f1f5f9; margin-top: 10px; padding-top: 20px;">
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Bed</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $f->bed ?? '0' }}</div>
                                        </div>

                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Table</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $f->table ?? '0' }}</div>
                                        </div>
                                        
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Chair</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $f->chair ?? '0' }}</div>
                                        </div>

                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Cupboard</div>
                                            <div style="font-size: 0.95rem; font-weight: 700; color: #334155;">{{ $f->cupboard ?? '0' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(isset($furnitures))
                    <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 60px; text-align: center;">
                        <h4 style="color: #1e293b; font-weight: 800; font-size: 1.5rem;">No Records Found</h4>
                        <p style="color: #64748b; margin-top: 10px;">We couldn't find any furniture matches. Try another search.</p>
                        <a href="{{ route('landing', ['mode' => 'room-furniture']) }}" class="btn" style="display: inline-block; background: #1e3a5f; color: white; border-radius: 50px; margin-top: 30px; width: auto; padding: 14px 40px; font-weight: 700; text-decoration: none;">RETRY SEARCH</a>
                    </div>
                @endif

                @if(isset($status_complaints))
                    @if(count($status_complaints) > 0)
                        <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 30px;">
                            <h4 style="color: #1e293b; margin-bottom: 25px; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                                <span style="background: #84cc16; width: 8px; height: 24px; border-radius: 4px;"></span>
                                Maintenance History ({{ count($status_complaints) }})
                            </h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
                                @foreach($status_complaints as $complaint)
                                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                            <div>
                                                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Ticket No</div>
                                                <div style="font-size: 1.5rem; font-weight: 900; color: #1e293b; letter-spacing: -0.5px;">#{{ $complaint->sno }}</div>
                                            </div>
                                            <div style="padding: 8px 16px; border-radius: 40px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; background: {{ $complaint->status == 'Done' ? '#f0fdf4' : '#fffbeb' }}; color: {{ $complaint->status == 'Done' ? '#15803d' : '#b45309' }}; border: 1px solid {{ $complaint->status == 'Done' ? '#dcfce7' : '#fef3c7' }};">
                                                {{ $complaint->status }}
                                            </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 12px;">
                                            <div>
                                                <div style="font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                                                <div style="font-size: 0.85rem; font-weight: 700; color: #334155;">{{ $complaint->created_at->format('d M, Y') }}</div>
                                            </div>
                                            <div>
                                                <div style="font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Location</div>
                                                <div style="font-size: 0.85rem; font-weight: 700; color: #334155;">Room {{ $complaint->room }}</div>
                                            </div>
                                        </div>
                                        
                                        <div style="flex: 1; background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9; border-left: 6px solid {{ $complaint->status == 'Done' ? '#84cc16' : '#f59e0b' }};">
                                            <p style="margin: 0 0 10px; font-size: 10px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Admin Resolution / Note</p>
                                            <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: #334155; font-weight: 500;">{{ $complaint->remark ?? 'Our team is processing this request. Please check back for updates.' }}</p>

                                            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #e2e8f0;">
                                                <p style="margin: 0 0 8px; font-size: 10px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Your Message</p>
                                                
                                                @if($complaint->user_reply)
                                                    <p style="margin: 0; font-size: 0.9rem; line-height: 1.5; color: #475569; font-style: italic;">"{{ $complaint->user_reply }}"</p>
                                                @else
                                                    <form action="{{ route('complaint.reply', $complaint->id) }}" method="POST">
                                                        @csrf
                                                        <textarea name="user_reply" rows="2" placeholder="Write a reply..." style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px; color: #334155; margin-bottom: 8px; font-family: inherit; resize: none; box-sizing: border-box;"></textarea>
                                                        <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 6px 15px; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">Send Reply</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- No status records found for this phone -->
                        <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 60px; text-align: center;">
                            <div style="font-size: 4rem; margin-bottom: 20px;">📂</div>
                            <h4 style="color: #1e293b; font-weight: 800; font-size: 1.5rem;">No History Found</h4>
                            <p style="color: #64748b; margin-top: 10px; max-width: 400px; margin-left: auto; margin-right: auto;">We couldn't find any pending maintenance records for <strong>{{ $search_phone }}</strong> in {{ $search_year }}.</p>
                            <a href="{{ route('landing', ['mode' => 'status']) }}" class="btn" style="display: inline-block; background: #1e3a5f; color: white; border-radius: 50px; margin-top: 30px; width: auto; padding: 14px 40px; font-weight: 700; text-decoration: none;">TRY ANOTHER SEARCH</a>
                        </div>
                    @endif
                @endif

                @if(isset($consumers) || isset($furnitures))
                    <div style="margin-top: 50px; text-align: center;">
                        <a href="{{ route('landing', ['mode' => 'sub-options']) }}" class="btn" style="display: inline-block; background: #1e3a5f; color: white; border-radius: 50px; padding: 20px 50px; font-weight: 800; width: auto; letter-spacing: 2px; box-shadow: 0 10px 30px rgba(30,58,95,0.25); text-decoration: none;">BACK</a>
                    </div>
                @elseif(isset($status_complaints) || isset($status_furniture))
                    <div style="margin-top: 50px; text-align: center;">
                        <a href="{{ route('landing') }}" class="btn" style="display: inline-block; background: #1e3a5f; color: white; border-radius: 50px; padding: 20px 50px; font-weight: 800; width: auto; letter-spacing: 2px; box-shadow: 0 10px 30px rgba(30,58,95,0.25); text-decoration: none;">BACK</a>
                    </div>
                @endif
                </div> <!-- End search-results -->

            </div>

            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        function togglePortalOptions() {
            document.getElementById('main-minimal-menu').style.display = 'none';
            document.getElementById('admin-login-btn-container').style.display = 'none';
            document.getElementById('portal-sub-options').style.display = 'block';
            document.getElementById('main-content-card').style.maxWidth = '900px';
        }

        function resetToMinimal() {
            document.getElementById('portal-sub-options').style.display = 'none';
            document.getElementById('main-minimal-menu').style.display = 'flex';
            document.getElementById('admin-login-btn-container').style.display = 'block';
            document.getElementById('main-content-card').style.maxWidth = '850px';
        }

        function selectMode(mode, role = null) {
            const wrapper = document.getElementById('landing-main-wrapper');
            const panel = document.getElementById('primary-panel');
            const card = document.getElementById('main-content-card');
            const resultsSection = document.getElementById('search-results');
            
            // Hide all sections
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('public-complaint-form').style.display = 'none';
            document.getElementById('public-furniture-form').style.display = 'none';
            document.getElementById('public-consumer-form').style.display = 'none';
            document.getElementById('public-status-form').style.display = 'none';
            document.getElementById('admin-login-form').style.display = 'none';
            if (resultsSection) resultsSection.style.display = 'none';
            
            // Auto-focus ID field if opening login
            if (mode === 'admin') {
                setTimeout(() => {
                    const idField = document.getElementById('login-id-field');
                    if (idField) idField.focus();
                }, 100);
            }
            


            // Toggle full-screen mode and card width
            // Force normal mode if explicitly going back to portal (mode === '')
            const hasResults = {{ (isset($status_complaints) || isset($consumers) || isset($furnitures) || isset($status_furniture)) ? 'true' : 'false' }};
            const isFullScreen = (mode !== '' && (mode === 'status' || mode === 'complaint' || mode === 'consumer' || mode === 'room-furniture' || mode === 'furniture-options' || hasResults));
            
            if (isFullScreen) {
                wrapper.classList.add('full-screen-mode');
                panel.classList.add('full-screen-mode');
                card.style.maxWidth = '1100px';
                // Show results if they exist
                if (hasResults && resultsSection) resultsSection.style.display = 'block';
            } else {
                wrapper.classList.remove('full-screen-mode');
                panel.classList.remove('full-screen-mode');
                card.style.maxWidth = (mode === 'portal' ? '900px' : '850px');
            }

            // Show relevant section
            if (mode === 'complaint') {
                document.getElementById('public-complaint-form').style.display = 'block';
            } else if (mode === 'consumer' || mode === 'room-furniture') {
                const formTitle = document.querySelector('#public-consumer-form .form-title');
                const formDesc = document.querySelector('#public-consumer-form p');
                const searchForm = document.querySelector('#public-consumer-form form');
                if (mode === 'room-furniture') {
                    if(formTitle) formTitle.innerText = 'Search Furniture List';
                    if(formDesc) formDesc.innerText = 'Locate your room furniture list by building and room.';
                    if(searchForm) searchForm.action = '{{ route("furniture.search") }}';
                } else {
                    if(formTitle) formTitle.innerText = 'Search Consumer ID';
                    if(formDesc) formDesc.innerText = 'Locate your consumer records by building and room.';
                    if(searchForm) searchForm.action = '{{ route("consumer.search") }}';
                }
                document.getElementById('public-consumer-form').style.display = 'block';
            } else if (mode === 'status') {
                document.getElementById('public-status-form').style.display = 'block';
            } else if (mode === 'admin') {
                document.getElementById('admin-login-form').style.display = 'block';
            } else if (mode === 'furniture-options') {
                document.getElementById('public-furniture-form').style.display = 'block';
            } else if (mode === 'sub-options') {
                document.getElementById('login-section').style.display = 'flex';
                togglePortalOptions();
            } else {
                // Default back to minimal portal menu
                document.getElementById('login-section').style.display = 'block';
                resetToMinimal();
            }
            
            // Scroll to top of panel when switching
            panel.scrollTop = 0;
        }

        function showLogin() {
            selectMode('');
        }

        // Run On Start
        window.addEventListener('load', () => {
            @if(session('active_mode'))
                selectMode('{{ session('active_mode') }}');
            @elseif(isset($active_mode))
                selectMode('{{ $active_mode }}');
            @elseif(isset($status_complaints) || isset($consumers) || isset($status_furniture))
                 document.getElementById('landing-main-wrapper').classList.add('full-screen-mode');
                 document.getElementById('primary-panel').classList.add('full-screen-mode');
                 document.getElementById('login-section').style.display = 'none';
                 if (document.getElementById('search-results')) {
                     document.getElementById('search-results').style.display = 'block';
                 }
            @else
                selectMode('');
            @endif
        });
        // Furniture form scripts
        const nextLendSno = {{ $nextLendSno ?? 1 }};
        const nextReturnSno = {{ $nextReturnSno ?? 1 }};

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
            const applicationInput = document.querySelector('#public-furniture-form input[name="application"]');
            if (value === 'Lend') {
                lendFields.style.display = 'grid';
                if(applicationInput) applicationInput.required = true;
            } else {
                lendFields.style.display = 'none';
                if(applicationInput) applicationInput.required = false;
            }
        }

        let furnitureRowCount = 1;
        function addFurnitureRow() {
            if (furnitureRowCount >= 5) return;

            const container = document.getElementById('furniture-items-container');
            const newRow = document.createElement('div');
            newRow.className = 'furniture-item-row';
            newRow.style.cssText = 'display: flex; gap: 12px; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.1); margin-top: 5px;';
            newRow.innerHTML = `
                <input type="text" name="items[${furnitureRowCount}][name]" required placeholder="Item ${furnitureRowCount + 1} description" style="flex: 4; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 14px;">
                <input type="number" name="items[${furnitureRowCount}][qty]" required placeholder="Qty" style="flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 14px;" min="1">
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
    </script>
@endsection
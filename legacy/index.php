<?php
session_start();

// Mock Credentials
$valid_user_id = "admin";
$valid_password = "123";

$message = "";

// Building List
$building_list = [];

// Room List (1-40)
$room_list = range(1, 40);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action == 'login') {
        $id = $_POST['id'];
        $pass = $_POST['password'];

        if ($id === $valid_user_id && $pass === $valid_password) {
            $_SESSION['loggedin'] = true;
            $_SESSION['target_view'] = $_POST['target_view'] ?? 'complaint';
        } else {
            $message = "Access Denied: Invalid Credentials";
        }
    }

    if ($action == 'submit_complaint') {
        // Save to Session Mock Database
        if (!isset($_SESSION['complaints'])) {
            $_SESSION['complaints'] = [];
        }

        // File Upload Handling
        $upload_dir = 'uploads/';
        $image_path = '';
        $video_path = '';

        if (!empty($_FILES['image']['name'])) {
            $target = $upload_dir . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = $target;
            }
        }

        if (!empty($_FILES['video']['name'])) {
            $target = $upload_dir . basename($_FILES['video']['name']);
            if (move_uploaded_file($_FILES['video']['tmp_name'], $target)) {
                $video_path = $target;
            }
        }

        // Auto S.No
        $sno = count($_SESSION['complaints']) + 1;

        $new_complaint = [
            'sno' => $sno,
            'name' => $_POST['name'],
            'building' => $_POST['building'],
            'room' => $_POST['room'],
            'phone' => $_POST['phone'],
            'complaint' => $_POST['complaint'],
            'datetime' => date('Y-m-d H:i:s'), // Auto Date Time
            'image' => $image_path,
            'video' => $video_path,
            'done' => false  // Track completion status
        ];

        $_SESSION['complaints'][] = $new_complaint;

        $message = "Electrical Complaint Filed Successfully! (ID: $sno)";
    }

    if ($action === 'submit_furniture') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $official_name = $_POST['official_name'];
        $type = $_POST['type'];
        $date = $_POST['date'] ?? ''; // Optional, can be added by admin later
        $remark = $_POST['remark'] ?? ''; // Optional, can be added by admin later

        // Collect all items and quantities
        $items = [];
        for ($i = 1; $i <= 6; $i++) {
            $item_name = $_POST["item_$i"] ?? '';
            $item_qty = $_POST["qty_$i"] ?? '';
            if (!empty($item_name) && !empty($item_qty)) {
                $items[] = [
                    'name' => $item_name,
                    'quantity' => $item_qty
                ];
            }
        }

        // File Upload Handling for Furniture Application
        $application_path = '';
        if (!empty($_FILES['application']['name'])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $target = $upload_dir . time() . '_' . basename($_FILES['application']['name']);
            if (move_uploaded_file($_FILES['application']['tmp_name'], $target)) {
                $application_path = $target;
            }
        }

        if (!isset($_SESSION['furniture'])) {
            $_SESSION['furniture'] = [];
        }

        $sno = count($_SESSION['furniture']) + 1;

        $_SESSION['furniture'][] = [
            'sno' => $sno,
            'name' => $name,
            'phone' => $phone,
            'official_name' => $official_name,
            'items' => $items,
            'type' => $type,
            'date' => $date,
            'remark' => $remark,
            'application' => $application_path,
            'datetime' => date('Y-m-d H:i:s')
        ];

        $message = "Furniture Log Updated Successfully!";
    }

    if ($action === 'submit_consumer') {
        $building = $_POST['building'];
        $room = $_POST['room'];
        $official_name = $_POST['official_name'] ?? '';

        // Search Logic using consumers.json
        $json_file = __DIR__ . '/consumers.json';
        $found_consumers = [];
        $search_performed = true;

        if (file_exists($json_file)) {
            $consumers_db = json_decode(file_get_contents($json_file), true);
            if ($consumers_db) {
                foreach ($consumers_db as $c) {
                    $b_match = (stripos($c['building'], $building) !== false) || (stripos($building, $c['building']) !== false);
                    
                    if ($building === 'Official' && !empty($official_name)) {
                        $name_match = stripos($c['name'], $official_name) !== false;
                        if ($b_match && $name_match) {
                            $found_consumers[] = $c;
                        }
                    } else {
                        $r_match = (string) $c['room'] === (string) $room;
                        if ($b_match && $r_match) {
                            $found_consumers[] = $c;
                        }
                    }
                }
            }
        }

        if (count($found_consumers) > 0) {
            $message = count($found_consumers) . " Consumer(s) Found.";
        } else {
            $search_context = (!empty($official_name)) ? "Name $official_name" : "Room $room";
            $message = "No consumer matched for $building, $search_context.";
        }
    }

    if ($action === 'toggle_done') {
        $sno = $_POST['sno'] ?? null;
        if ($sno && isset($_SESSION['complaints'])) {
            foreach ($_SESSION['complaints'] as &$complaint) {
                if ($complaint['sno'] == $sno) {
                    $complaint['done'] = !($complaint['done'] ?? false);
                    echo json_encode(['success' => true, 'done' => $complaint['done']]);
                    exit;
                }
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'clear_consumers') {
        $_SESSION['consumers'] = [];
        $message = "Consumer Registry Cleared!";
    }

    if ($action === 'logout') {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php if (!isset($_SESSION['loggedin'])): ?>
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h2>Sign In</h2>
                </div>

                <!-- Selection Mode -->
                <div class="boxes-container">
                    <div class="dashboard-box" id="box-complaint" onclick="selectMode('complaint')">
                        <div class="icon">⚡</div> <!-- Changed Icon -->
                        <div class="label">Electrical Complaint</div> <!-- Renamed -->
                    </div>
                    <div class="dashboard-box" id="box-consumer" onclick="selectMode('consumer')">
                        <div class="icon">🆔</div>
                        <div class="label">Consumer ID</div>
                    </div>
                    <div class="dashboard-box" id="box-furniture" onclick="selectMode('furniture')">
                        <div class="icon">🪑</div>
                        <div class="label">Furniture Lend and Return</div>
                    </div>
                </div>

                <div id="message-container">
                    <?php if ($message): ?>
                        <p style="color: red; font-size: 14px; text-align: center;"><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>

                <!-- LOGIN FORM (Default) -->
                <div id="login-section">
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        <input type="hidden" name="target_view" id="target_view" value="complaint">

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="id" placeholder="Username" required>
                        </div>

                        <div class="form-group">
                            <label>Password <a href="#" class="forgot-password">Forgot your password?</a></label>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>

                        <button type="submit" class="btn btn-sign-in">Sign In</button>
                    </form>
                </div>

                <!-- PUBLIC ELECTRICAL COMPLAINT FORM -->
                <div id="public-complaint-form" style="display:none;">
                    <h3>New Electrical Complaint</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="submit_complaint">

                        <div class="landscape-form-grid">
                            <div class="form-group"><label>S.No (Auto)</label><input type="text"
                                    value="<?php echo count($_SESSION['complaints'] ?? []) + 1; ?>" disabled
                                    style="background: #eee;"></div>
                            <div class="form-group"><label>Date & Time (Auto)</label><input type="text"
                                    value="<?php echo date('Y-m-d H:i:s'); ?>" disabled style="background: #eee;"></div>

                            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
                            <div class="form-group"><label>Phone</label><input type="text" name="phone" required></div>

                            <div class="form-group"><label>Building</label>
                                <select name="building" required style="width:100%; padding:10px; border:1px solid #ccc;">
                                    <option value="">Select Building</option>
                                    <?php foreach ($building_list as $b) {
                                        echo "<option value='$b'>$b</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Room No</label>
                                <select name="room" required style="width:100%; padding:10px; border:1px solid #ccc;">
                                    <option value="">Select Room</option>
                                    <?php foreach ($room_list as $r) {
                                        echo "<option value='$r'>$r</option>";
                                    } ?>
                                </select>
                            </div>

                            <div class="form-group full-width"><label>Complaint</label><textarea name="complaint" rows="3"
                                    required></textarea></div>

                            <div class="form-group"><label>Image Evidence</label><input type="file" name="image"
                                    accept="image/*"></div>
                            <div class="form-group"><label>Video Evidence</label><input type="file" name="video"
                                    accept="video/*"></div>
                        </div>

                        <button type="submit" class="btn" style="margin-top:20px;">Submit Complaint</button>
                        <button type="button" class="btn" style="background: #ddd; margin-top: 10px;"
                            onclick="showLogin()">Back to Login</button>
                    </form>
                </div>

                <!-- PUBLIC FURNITURE FORM -->
                <div id="public-furniture-form" style="display:none;">
                    <h3>Furniture Log</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="submit_furniture">

                        <div class="landscape-form-grid">
                            <div class="form-group"><label>S.No (Auto)</label><input type="text"
                                    value="<?php echo count($_SESSION['furniture'] ?? []) + 1; ?>" disabled
                                    style="background: #eee;"></div>
                            <div class="form-group"><label>Date & Time (Auto)</label><input type="text"
                                    value="<?php echo date('Y-m-d H:i:s'); ?>" disabled style="background: #eee;"></div>

                            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
                            <div class="form-group"><label>Phone Number</label><input type="text" name="phone" required>
                            </div>

                            <div class="form-group"><label>Official Name</label><input type="text" name="official_name"
                                    required></div>

                            <div class="form-group"><label>Action</label>
                                <select name="type" id="furniture-type" onchange="toggleFurnitureApplication()"
                                    style="width:100%; padding:10px; border:1px solid #ccc;">
                                    <option>Lend</option>
                                    <option>Return</option>
                                </select>
                            </div>

                            <div class="form-group" id="furniture-application-group">
                                <label>Application (PDF/Image)</label>
                                <input type="file" name="application" accept=".pdf,image/*">
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="margin-top: 0; color: #333;">Items (Up to 6)</h4>
                            <div id="items-container">
                                <!-- Item 1 (Always visible) -->
                                <div class="item-row" id="item-row-1" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 1</label>
                                        <input type="text" name="item_1" id="item_1" onkeyup="checkItemField(1)" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_1" id="qty_1" onkeyup="checkItemField(1)" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                                <!-- Item 2-6 (Hidden initially) -->
                                <div class="item-row" id="item-row-2" style="display: none; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 2</label>
                                        <input type="text" name="item_2" id="item_2" onkeyup="checkItemField(2)" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_2" id="qty_2" onkeyup="checkItemField(2)" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="item-row" id="item-row-3" style="display: none; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 3</label>
                                        <input type="text" name="item_3" id="item_3" onkeyup="checkItemField(3)" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_3" id="qty_3" onkeyup="checkItemField(3)" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="item-row" id="item-row-4" style="display: none; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 4</label>
                                        <input type="text" name="item_4" id="item_4" onkeyup="checkItemField(4)" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_4" id="qty_4" onkeyup="checkItemField(4)" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="item-row" id="item-row-5" style="display: none; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 5</label>
                                        <input type="text" name="item_5" id="item_5" onkeyup="checkItemField(5)" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_5" id="qty_5" onkeyup="checkItemField(5)" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="item-row" id="item-row-6" style="display: none; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label>Item 6</label>
                                        <input type="text" name="item_6" id="item_6" placeholder="Enter item name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label>No. of Items</label>
                                        <input type="number" name="qty_6" id="qty_6" min="1" placeholder="Quantity" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn" style="margin-top:20px;">Submit Log</button>
                        <button type="button" class="btn" style="background: #ddd; margin-top: 10px;"
                            onclick="showLogin()">Back to Login</button>
                    </form>
                </div>

                <!-- PUBLIC CONSUMER FORM -->
                <div id="public-consumer-form" style="display:none;">
                    <h3>Find the Consumer ID</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="submit_consumer">

                        <div class="landscape-form-grid">
                            <div class="form-group"><label>Building Name</label>
                                <select name="building" id="consumer-building" required onchange="toggleOfficialField()" style="width:100%; padding:10px; border:1px solid #ccc;">
                                    <option value="">Select Building</option>
                                    <?php foreach ($building_list as $b) {
                                        echo "<option value='$b'>$b</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group" id="room-group"><label>Room Number</label>
                                <select name="room" id="consumer-room" style="width:100%; padding:10px; border:1px solid #ccc;">
                                    <option value="">Select Room</option>
                                    <?php foreach ($room_list as $r) {
                                        echo "<option value='$r'>$r</option>";
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group" id="official-name-group" style="display:none;"><label>Official Name</label>
                                <input type="text" name="official_name" placeholder="Enter Official Name" style="width:100%; padding:10px; border:1px solid #ccc; border-radius: 4px;">
                            </div>
                        </div>

                        <button type="submit" class="btn" style="margin-top: 20px;">Search</button>
                        <button type="button" class="btn" style="background: #ddd; margin-top: 10px;"
                            onclick="showLogin()">Back to Login</button>
                    </form>

                    <?php if (isset($search_performed) && $search_performed): ?>
                        <div
                            style="margin-top: 20px; padding: 20px; border: 2px solid #28a745; background: #fff; border-radius: 8px;">
                            <?php if (!empty($found_consumers)): ?>
                                <h4
                                    style="margin-top:0; color: green; font-size: 1.5rem; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px;">
                                    Result(s) Found (<?php echo count($found_consumers); ?>)</h4>
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                                    <?php foreach ($found_consumers as $index => $found_consumer): ?>
                                        <div style="background: #f0fff4; border: 1px solid #28a745; border-radius: 8px; padding: 15px; font-size: 1rem; line-height: 1.4; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <p style="margin: 5px 0; border-bottom: 1px solid #c3e6cb; padding-bottom: 5px;"><strong>Consumer ID:</strong> <br><span
                                                    style="font-size: 1.2rem; color: #2e7d32;"><?php echo htmlspecialchars($found_consumer['cid']); ?></span>
                                            </p>
                                            <p style="margin: 5px 0;"><strong>IN ID:</strong> <?php echo htmlspecialchars($found_consumer['inid'] ?? 'N/A'); ?></p>
                                            <p style="margin: 5px 0;"><strong>Meter No:</strong> <?php echo htmlspecialchars($found_consumer['meter'] ?? 'N/A'); ?></p>
                                            <p style="margin: 8px 0; min-height: 2.8em;"><strong>Name:</strong><br><?php echo htmlspecialchars($found_consumer['name']); ?></p>
                                            
                                            <div style="font-size: 0.9rem; color: #666; background: rgba(0,0,0,0.02); padding: 8px; border-radius: 4px; margin-top: 10px;">
                                                <?php if (!empty($found_consumer['account'])): ?>
                                                    <div><strong>A/C:</strong> <?php echo htmlspecialchars($found_consumer['account']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($found_consumer['department'])): ?>
                                                    <div><strong>Dept:</strong> <?php echo htmlspecialchars($found_consumer['department']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($found_consumer['installation'])): ?>
                                                    <div><strong>Inst:</strong> <?php echo htmlspecialchars($found_consumer['installation']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($found_consumer['phase'])): ?>
                                                    <div><strong>Phase:</strong> <?php echo htmlspecialchars($found_consumer['phase']); ?></div>
                                                <?php endif; ?>
                                                <div style="margin-top: 5px; color: #444;"><strong><?php echo htmlspecialchars($found_consumer['building']); ?></strong> - Room <?php echo htmlspecialchars($found_consumer['room']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <h4 style="margin-top:0; color: red; font-size: 1.5rem;">No Result</h4>
                                <p style="font-size: 1.2rem;">No consumer found for the selected building and search criteria.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="brand-logo">
                    <!-- Simple SVG Leaf Icon Placeholder -->
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <path
                            d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z" />
                    </svg>
                    Shangkang
                </div>
            </div>
        </div>

        <script>
            function selectMode(mode) {
                // Clear message if moving to a new mode (or back to login)
                const msgContainer = document.getElementById('message-container');
                if (msgContainer) msgContainer.innerHTML = '';

                // Determine visibility based on mode
                document.getElementById('login-section').style.display = 'none';
                document.getElementById('public-complaint-form').style.display = 'none';
                document.getElementById('public-furniture-form').style.display = 'none';
                document.getElementById('public-consumer-form').style.display = 'none';

                // Reset Card Width
                document.querySelector('.login-card').classList.remove('wide-mode');

                if (mode === 'complaint') {
                    document.getElementById('public-complaint-form').style.display = 'block';
                    document.querySelector('.login-card').classList.add('wide-mode'); // Widen for Landscape
                } else if (mode === 'furniture') {
                    document.getElementById('public-furniture-form').style.display = 'block';
                    document.querySelector('.login-card').classList.add('wide-mode'); // Widen for Landscape
                    toggleFurnitureApplication();
                } else if (mode === 'consumer') {
                    document.getElementById('public-consumer-form').style.display = 'block';
                    document.querySelector('.login-card').classList.add('wide-mode'); // Widen for Landscape
                } else {
                    document.getElementById('login-section').style.display = 'block';
                }

                document.getElementById('target_view').value = mode;

                // Highlight Box
                document.getElementById('box-complaint').classList.remove('selected');
                document.getElementById('box-furniture').classList.remove('selected');
                document.getElementById('box-consumer').classList.remove('selected');
                if (mode) document.getElementById('box-' + mode).classList.add('selected');
            }

            function showLogin() {
                selectMode('');
                // Deselect boxes
                document.getElementById('box-complaint').classList.remove('selected');
                document.getElementById('box-furniture').classList.remove('selected');
                document.getElementById('box-consumer').classList.remove('selected');
            }

            function toggleOfficialField() {
                const building = document.getElementById('consumer-building').value;
                const roomGroup = document.getElementById('room-group');
                const officialGroup = document.getElementById('official-name-group');
                const roomSelect = document.getElementById('consumer-room');

                if (building === 'Official') {
                    roomGroup.style.display = 'none';
                    officialGroup.style.display = 'block';
                    roomSelect.required = false;
                } else {
                    roomGroup.style.display = 'block';
                    officialGroup.style.display = 'none';
                    roomSelect.required = true;
                }
            }

            function toggleFurnitureApplication() {
                const type = document.getElementById('furniture-type').value;
                const appGroup = document.getElementById('furniture-application-group');
                if (type === 'Lend') {
                    appGroup.style.display = 'block';
                } else {
                    appGroup.style.display = 'none';
                }
            }

            function checkItemField(rowNum) {
                const itemInput = document.getElementById('item_' + rowNum);
                const qtyInput = document.getElementById('qty_' + rowNum);
                
                // Check if both fields have values
                if (itemInput && qtyInput && itemInput.value.trim() !== '' && qtyInput.value.trim() !== '') {
                    // Show next row if it exists and is not already visible
                    const nextRow = rowNum + 1;
                    if (nextRow <= 6) {
                        const nextRowElement = document.getElementById('item-row-' + nextRow);
                        if (nextRowElement && nextRowElement.style.display === 'none') {
                            nextRowElement.style.display = 'grid';
                        }
                    }
                }
            }

            function toggleAdminOfficialField() {
                const building = document.getElementById('admin-consumer-building').value;
                const roomGroup = document.getElementById('admin-room-group');
                const officialGroup = document.getElementById('admin-official-name-group');
                const roomSelect = document.getElementById('admin-consumer-room');

                if (building === 'Official') {
                    roomGroup.style.display = 'none';
                    officialGroup.style.display = 'block';
                    roomSelect.required = false;
                } else {
                    roomGroup.style.display = 'block';
                    officialGroup.style.display = 'none';
                    roomSelect.required = true;
                }
            }

            // Default to Login view, not Complaint form
            <?php if (isset($search_performed) && $search_performed): ?>
                selectMode('consumer');
                toggleOfficialField();
            <?php else: ?>
                showLogin();
            <?php endif; ?>
        </script>

    <?php else: ?>
        <!-- DASHBOARD (Adapted to new clean style) -->
        <div class="inner-paper">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #000000; border-radius: 8px 8px 0 0; margin: -20px -20px 20px -20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="margin: 0; color: white; font-size: 24px; font-weight: 600;">Admin Dashboard</h2>
                <form method="POST" style="margin: 0;">
                    <button type="submit" name="action" value="logout" class="btn"
                        style="width: auto; padding: 8px 20px; font-size: 14px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; font-weight: 500; transition: all 0.3s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
                        onmouseout="this.style.background='rgba(255,255,255,0.2)'">Logout</button>
                </form>
            </div>

            <!-- Dashboard Content Reuse -->
            <div id="dashboard-view" <?php echo (($_SESSION['target_view'] ?? '') !== 'complaint' && ($_SESSION['target_view'] ?? '') !== '') ? 'style="display:none;"' : ''; ?>>
                <?php
                // Calculate statistics
                $total_complaints = count($_SESSION['complaints'] ?? []);
                $done_complaints = 0;
                foreach ($_SESSION['complaints'] ?? [] as $c) {
                    if ($c['done'] ?? false) {
                        $done_complaints++;
                    }
                }
                $incomplete_complaints = $total_complaints - $done_complaints;

                $furniture_items = $_SESSION['furniture'] ?? [];
                $total_lend = 0;
                $total_return = 0;
                foreach ($furniture_items as $item) {
                    if (isset($item['type'])) {
                        if ($item['type'] === 'Lend') {
                            $total_lend++;
                        } elseif ($item['type'] === 'Return') {
                            $total_return++;
                        }
                    }
                }
                ?>

                <!-- Dashboard Summary Boxes -->
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                    <!-- Electrical Complaint Box (Full Width on Top) -->
<div onclick="showComplaintTable()"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 40px; color: white; cursor: pointer; transition: opacity 0.3s; min-height: 240px; display: flex; flex-direction: column; justify-content: space-between;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <!-- Title at Top Center -->
                        <div style="font-size: 40px; opacity: 0.9; font-weight: 600; text-align: center;">⚡ Electrical Complaint</div>
                        
                        <!-- Three Statistics at Bottom -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px;">
                            <!-- Total Complaints -->
                            <div style="text-align: center;">
                                <div style="font-size: 75px; font-weight: bold; margin-bottom: 8px;"><?php echo $total_complaints; ?></div>
                                <div style="font-size: 26px; opacity: 0.8;">Total Complaints</div>
                            </div>
                            
                            <!-- Done -->
                            <div style="text-align: center;">
                                <div style="font-size: 75px; font-weight: bold; margin-bottom: 8px;"><?php echo $done_complaints; ?></div>
                                <div style="font-size: 26px; opacity: 0.8;">Done</div>
                            </div>
                            
                            <!-- Incomplete -->
                            <div style="text-align: center;">
                                <div style="font-size: 75px; font-weight: bold; margin-bottom: 8px;"><?php echo $incomplete_complaints; ?></div>
                                <div style="font-size: 26px; opacity: 0.8;">Incomplete</div>
                            </div>
                        </div>
                    </div>

                    <!-- Furniture Boxes Row (Below Electrical Complaint) -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <!-- Furniture Lend Box -->
                        <div onclick="showFurnitureTable()"
                            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 28px; color: white; cursor: pointer; transition: opacity 0.3s; display: flex; flex-direction: column; align-items: center; justify-content: space-between; min-height: 170px;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <div style="font-size: 36px; opacity: 0.9; font-weight: 600; text-align: center;">🪑 Furniture Lend</div>
                            <div style="text-align: center;">
                                <div style="font-size: 60px; font-weight: bold; margin-bottom: 5px;"><?php echo $total_lend; ?></div>
                                <div style="font-size: 22px; opacity: 0.8;">Items Lent</div>
                            </div>
                        </div>

                        <!-- Furniture Return Box -->
                        <div onclick="showFurnitureTable()"
                            style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 28px; color: white; cursor: pointer; transition: opacity 0.3s; display: flex; flex-direction: column; align-items: center; justify-content: space-between; min-height: 170px;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <div style="font-size: 36px; opacity: 0.9; font-weight: 600; text-align: center;">🔄 Furniture Return</div>
                            <div style="text-align: center;">
                                <div style="font-size: 60px; font-weight: bold; margin-bottom: 5px;"><?php echo $total_return; ?></div>
                                <div style="font-size: 22px; opacity: 0.8;">Items Returned</div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Complaint Table View -->
            <div id="complaint-table-view" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 0 4px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: #667eea; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);">⚡</div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.25rem; font-weight: 700;">Electrical Complaint Details</h3>
                    </div>
                    <button onclick="showDashboard()" class="btn" style="width: auto; padding: 10px 18px; font-size: 13px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b';">
                        <span style="font-size: 16px;">←</span> Back to Dashboard
                    </button>
                </div>
                <div style="border-top: 1px solid #ddd; margin-top: 15px;">
                    <table class="complaint-table" style="border-left: none; border-right: none; margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">S.No</th>
                                <th style="width: 160px;">Date & Time</th>
                                <th>Name & Location</th>
                                <th style="width: 120px;">Phone</th>
                                <th>Complaint</th>
                                <th style="width: 80px;">Files</th>
                                <th style="width: 200px;">Remark</th>
                                <th style="width: 60px;">Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['complaints'])): ?>
                                <?php foreach (array_reverse($_SESSION['complaints']) as $c): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['sno']); ?></td>
                                        <td><?php echo htmlspecialchars($c['datetime'] ?? '-'); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($c['name']); ?></strong><br>
                                            <span style="font-size: 0.85em; color: #64748b;"><?php echo htmlspecialchars($c['building'] ?? '-'); ?> - Room <?php echo htmlspecialchars($c['room'] ?? '-'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($c['complaint']); ?></td>
                                        <td>
                                            <?php if (!empty($c['image'])): ?>
                                                <a href="<?php echo $c['image']; ?>" target="_blank">Image</a>
                                            <?php endif; ?>
                                            <?php if (!empty($c['video'])): ?>
                                                <a href="<?php echo $c['video']; ?>" target="_blank">Video</a>
                                            <?php endif; ?>
                                            <?php if (empty($c['image']) && empty($c['video'])): ?>-<?php endif; ?>
                                        </td>
                                        <td><input type="text" placeholder="Add remark..."
                                                style="width: 100%; padding: 5px; border: 1px solid #ccc;"></td>
                                        <td style="text-align: center;"><input type="checkbox"
                                                onclick="toggleDone(<?php echo $c['sno']; ?>, this)"
                                                <?php echo ($c['done'] ?? false) ? 'checked' : ''; ?>
                                                style="width: 20px; height: 20px; cursor: pointer;"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" style="text-align:center; padding: 20px; color: #999;">No complaints
                                        recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Furniture Table View -->
            <div id="furniture-table-view" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 0 4px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: #f093fb; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 6px rgba(240, 147, 251, 0.25);">🪑</div>
                        <h3 style="margin: 0; color: #1e293b; font-size: 1.25rem; font-weight: 700;">Furniture Records</h3>
                    </div>
                    <button onclick="showDashboard()" class="btn" style="width: auto; padding: 10px 18px; font-size: 13px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b';">
                        <span style="font-size: 16px;">←</span> Back to Dashboard
                    </button>
                </div>
                <div style="border-top: 1px solid #ddd;">
                    <table class="complaint-table" style="border-left: none; border-right: none; margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">S.No</th>
                                <th style="width: 160px;">Date & Time</th>
                                <th>Name & Official</th>
                                <th style="width: 120px;">Phone</th>
                                <th>Items (Quantity)</th>
                                <th style="width: 100px;">Action</th>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 80px;">App.</th>
                                <th style="width: 200px;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['furniture'])): ?>
                                <?php foreach (array_reverse($_SESSION['furniture']) as $f): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($f['sno']); ?></td>
                                        <td><?php echo htmlspecialchars($f['datetime'] ?? '-'); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($f['name']); ?></strong><br>
                                            <span style="font-size: 0.85em; color: #64748b;"><?php echo htmlspecialchars($f['official_name'] ?? '-'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($f['phone'] ?? '-'); ?></td>
                                        <td>
                                            <?php 
                                            if (isset($f['items']) && is_array($f['items']) && count($f['items']) > 0) {
                                                $itemsList = [];
                                                foreach ($f['items'] as $item) {
                                                    $itemsList[] = htmlspecialchars($item['name']) . ' (' . htmlspecialchars($item['quantity']) . ')';
                                                }
                                                echo implode('<br>', $itemsList);
                                            } elseif (isset($f['item'])) {
                                                echo htmlspecialchars($f['item']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><span style="padding: 4px 8px; border-radius: 4px; background: <?php echo $f['type'] === 'Lend' ? '#ffeaa7' : '#74b9ff'; ?>; color: #2d3436;"><?php echo htmlspecialchars($f['type']); ?></span></td>
                                        <td><input type="date" value="<?php echo htmlspecialchars($f['date'] ?? ''); ?>" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"></td>
                                        <td>
                                            <?php if (!empty($f['application'])): ?>
                                                <a href="<?php echo htmlspecialchars($f['application']); ?>" target="_blank" style="color: #007bff; text-decoration: underline;">View</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><input type="text" placeholder="Add remark..." value="<?php echo htmlspecialchars($f['remark'] ?? ''); ?>" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" style="text-align:center; padding: 20px; color: #999;">No furniture records yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Furniture Views -->
            <div id="furniture-form-view" <?php echo (($_SESSION['target_view'] ?? '') === 'furniture') ? 'style="display:block;"' : 'style="display:none;"'; ?>>
                <h3>Furniture Log</h3>
                <div
                    style="border: 1px solid #eee; padding: 10px; margin-bottom: 20px; max-height: 150px; overflow-y:auto;">
                    <?php if (!empty($_SESSION['furniture'])): ?>
                        <?php foreach (array_reverse($_SESSION['furniture']) as $f): ?>
                            <div><strong><?php echo htmlspecialchars($f['sno'] ?? '-'); ?></strong>.
                                <?php echo htmlspecialchars($f['item']); ?> (<?php echo htmlspecialchars($f['type']); ?>) -
                                <?php echo htmlspecialchars($f['name']); ?> (<?php echo htmlspecialchars($f['datetime'] ?? ''); ?>)
                                <?php if (!empty($f['application'])): ?>
                                    | <a href="<?php echo htmlspecialchars($f['application']); ?>" target="_blank" style="color: blue;">View Application</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color:#999;">No records.</div>
                    <?php endif; ?>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="submit_furniture">

                    <div class="form-group">
                        <label>S.No (Auto)</label>
                        <input type="text" value="<?php echo count($_SESSION['furniture'] ?? []) + 1; ?>" disabled
                            style="background: #eee;">
                    </div>

                    <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Phone Number</label><input type="text" name="phone" required></div>
                    <div class="form-group"><label>Official Name</label><input type="text" name="official_name" required>
                    </div>

                    <div class="form-group"><label>Date & Time (Auto)</label><input type="text"
                            value="<?php echo date('Y-m-d H:i:s'); ?>" disabled style="background: #eee;"></div>

                    <div class="form-group"><label>Item</label><input type="text" name="item" required></div>

                    <div class="form-group"><label>Action</label><select name="type"
                            style="width:100%; padding:10px; border:1px solid #ccc;">
                            <option>Lend</option>
                            <option>Return</option>
                        </select></div>
                    <div style="display:flex; gap: 10px;">
                        <button type="submit" class="btn">Submit Log</button>
                    </div>
                </form>
            </div>

            <!-- Consumer ID Views -->
            <div id="consumer-form-view" <?php echo (($_SESSION['target_view'] ?? '') === 'consumer') ? 'style="display:block;"' : 'style="display:none;"'; ?>>
                <h3>Consumer ID Registry</h3>
                <div
                    style="border: 1px solid #eee; padding: 10px; margin-bottom: 20px; max-height: 150px; overflow-y:auto;">
                    <?php if (!empty($_SESSION['consumers'])): ?>
                        <?php foreach (array_reverse($_SESSION['consumers']) as $co): ?>
                            <div><strong><?php echo htmlspecialchars($co['building']); ?></strong> -
                                Room: <?php echo htmlspecialchars($co['room']); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color:#999;">No consumers registered.</div>
                    <?php endif; ?>
                </div>

                <form method="POST" onsubmit="return confirm('Are you sure you want to delete all consumer data?');"
                    style="margin-bottom: 20px;">
                    <button type="submit" name="action" value="clear_consumers" class="btn"
                        style="background: #ffcccc; color: #a00; border-color: #e00;">Clear Registry</button>
                </form>

                <form method="POST">
                    <input type="hidden" name="action" value="submit_consumer">
                    <div class="form-group"><label>Building Name</label>
                        <select name="building" id="admin-consumer-building" required onchange="toggleAdminOfficialField()" style="width:100%; padding:10px; border:1px solid #ccc;">
                            <option value="">Select Building</option>
                            <?php foreach ($building_list as $b) {
                                echo "<option value='$b'>$b</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group" id="admin-room-group"><label>Room Number</label>
                        <select name="room" id="admin-consumer-room" style="width:100%; padding:10px; border:1px solid #ccc;">
                            <option value="">Select Room</option>
                            <?php foreach ($room_list as $r) {
                                echo "<option value='$r'>$r</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="form-group" id="admin-official-name-group" style="display:none;"><label>Official Name</label>
                        <input type="text" name="official_name" placeholder="Enter Official Name" style="width:100%; padding:10px; border:1px solid #ccc; border-radius: 4px;">
                    </div>
                    <button type="submit" class="btn">Search Consumer</button>
                </form>
            </div>

            <?php if ($message): ?>
                <div
                    style="background: #dff0d8; color: #3c763d; padding: 10px; border-radius: 4px; margin-top: 20px; text-align: center;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <script>
        function showComplaintTable() {
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('complaint-table-view').style.display = 'block';
            document.getElementById('furniture-table-view').style.display = 'none';
        }

        function showFurnitureTable() {
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('complaint-table-view').style.display = 'none';
            document.getElementById('furniture-table-view').style.display = 'block';
        }

        function showDashboard() {
            document.getElementById('dashboard-view').style.display = 'block';
            document.getElementById('complaint-table-view').style.display = 'none';
            document.getElementById('furniture-table-view').style.display = 'none';
        }

        function toggleDone(sno, checkbox) {
            const formData = new FormData();
            formData.append('action', 'toggle_done');
            formData.append('sno', sno);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    checkbox.checked = !checkbox.checked;
                    alert('Failed to update status');
                }
            })
            .catch(error => {
                checkbox.checked = !checkbox.checked;
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>


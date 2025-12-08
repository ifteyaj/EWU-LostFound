<?php
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Error messages mapping
$error_messages = [
    'invalid_token' => 'Security validation failed. Please try again.',
    'missing_fields' => 'Please fill in all required fields.',
    'invalid_email' => 'Please enter a valid email address.',
    'invalid_date' => 'Please enter a valid date.',
    'file_too_large' => 'Image file is too large. Maximum size is 2MB.',
    'invalid_file_type' => 'Invalid file type. Only JPG, PNG, GIF, WEBP are allowed.',
    'upload_failed' => 'Failed to upload image. Please try again.',
    'database_error' => 'Database error occurred. Please try again later.'
];

$error = isset($_GET['error']) && isset($error_messages[$_GET['error']]) 
    ? $error_messages[$_GET['error']] 
    : null;

// Handle form submission - Mapping inputs to handlers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    
    // Common mappings
    if ($type == 'lost') {
        $_POST['last_location'] = $_POST['location'];
        $_POST['student_name'] = $_POST['contact_name'];
        $_POST['student_id'] = $_POST['contact_id'];
        include 'handlers/handle_lost.php';
    } else {
        $_POST['found_location'] = $_POST['location'];
        $_POST['finder_name'] = $_POST['contact_name'];
        $_POST['finder_id'] = $_POST['contact_id'];
        $_POST['date_found'] = $_POST['date_lost'];
        include 'handlers/handle_found.php';
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Item - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .toggle-switch input[type="radio"] {
            display: none;
        }
        .error-banner {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }
        .error-banner::before {
            content: "⚠️";
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <img src="assets/img/logo.png" alt="EWU Lost & Found">
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">My Account</a></li>
                <li><button class="theme-toggle" title="Toggle theme">🌙</button></li>
                <li><a href="post_item.php" class="btn-pill">Report Item</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <div>
                    <h2>Report an Item</h2>
                    <p>Help us reunite items with their owners.</p>
                </div>
                <div class="toggle-switch">
                    <input type="radio" name="type_toggle" id="toggle_lost" value="lost" checked>
                    <label for="toggle_lost" class="toggle-btn active" onclick="setType('lost')">Lost</label>
                    <input type="radio" name="type_toggle" id="toggle_found" value="found">
                    <label for="toggle_found" class="toggle-btn" onclick="setType('found')">Found</label>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-banner"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="post_item.php" method="POST" enctype="multipart/form-data" id="reportForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="type" id="itemType" value="lost">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>WHAT WAS IT?</label>
                        <input type="text" name="item_name" class="form-control" placeholder="Item Name (e.g. Blue Hydroflask)" required>
                    </div>

                    <div class="form-group">
                        <label>CATEGORY</label>
                        <select name="category" class="form-control" required>
                            <option value="Electronics">⌨️ Electronics</option>
                            <option value="Accessories">👜 Accessories</option>
                            <option value="Documents">📄 Documents</option>
                            <option value="Clothing">👕 Clothing</option>
                            <option value="Books">📚 Books & Stationery</option>
                            <option value="IDs">🪪 IDs & Cards</option>
                            <option value="Keys">🔑 Keys</option>
                            <option value="Other">📦 Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>WHEN?</label>
                        <input type="date" name="date_lost" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>WHERE?</label>
                        <input type="text" name="location" class="form-control" placeholder="Specific Location" required>
                    </div>

                    <div class="form-group full-width">
                        <label>DESCRIPTION</label>
                        <textarea name="description" class="form-control" placeholder="Brief details about the item..." required></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label class="upload-area" for="imageUpload">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">Upload Photo (Max 2MB)</div>
                            <input type="file" name="image" id="imageUpload" accept="image/jpeg,image/png,image/gif,image/webp">
                        </label>
                    </div>
                </div>

                <!-- Contact Info -->
                <div style="border-top: 1px solid var(--border-light); margin: 1.5rem 0; padding-top: 1.5rem;">
                    <label style="margin-bottom: 1rem; display:block; font-weight:600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary);">Contact Information</label>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>YOUR NAME</label>
                            <input type="text" name="contact_name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <label>STUDENT ID</label>
                            <input type="text" name="contact_id" class="form-control" placeholder="e.g. 2020-3-60-001" required>
                        </div>
                        <div class="form-group full-width">
                            <label>EMAIL</label>
                            <input type="email" name="email" class="form-control" placeholder="your.email@ewubd.edu" required>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="index.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-pill btn-lg" id="submitBtn">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setType(type) {
            document.getElementById('itemType').value = type;
            const lostBtn = document.querySelector('label[for="toggle_lost"]');
            const foundBtn = document.querySelector('label[for="toggle_found"]');
            
            if (type === 'lost') {
                lostBtn.classList.add('active');
                foundBtn.classList.remove('active');
            } else {
                foundBtn.classList.add('active');
                lostBtn.classList.remove('active');
            }
        }

        // Show selected file name and validate size
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File is too large. Maximum size is 2MB.');
                    this.value = '';
                    return;
                }
                document.querySelector('.upload-text').textContent = file.name;
            }
        });

        // Form submission loading state
        document.getElementById('reportForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.textContent = 'Submitting...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

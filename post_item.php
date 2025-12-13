<?php
/**
 * Report Item Page
 * Form for reporting lost or found items
 */

require_once 'init.php';

// Require user to be logged in
requireLogin();

// Get current user data
$currentUser = getCurrentUser();

// Generate CSRF token using security module
$csrf_token = generateCsrfToken();

// Error messages mapping
$error_messages = [
    'invalid_token' => 'Security validation failed. Please try again.',
    'missing_fields' => 'Please fill in all required fields.',
    'invalid_email' => 'Please enter a valid email address.',
    'invalid_date' => 'Please enter a valid date.',
    'invalid_category' => 'Please select a valid category.',
    'file_too_large' => 'Image file is too large. Maximum size is 2MB.',
    'invalid_file_type' => 'Invalid file type. Only JPG, PNG, GIF, WEBP are allowed.',
    'invalid_file' => 'Invalid file. Please check the file type and size.',
    'upload_failed' => 'Failed to upload image. Please try again.',
    'database_error' => 'Database error occurred. Please try again later.',
    'rate_limit' => 'Too many requests. Please wait a moment and try again.'
];

$error = isset($_GET['error']) && isset($error_messages[$_GET['error']]) 
    ? $error_messages[$_GET['error']] 
    : null;

// Handle form submission - Mapping inputs to handlers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'] ?? 'lost';
    
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
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .report-form-card {
            max-width: 700px;
            margin: 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: 24px;
            padding: 2.5rem;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .report-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }
        .report-header p {
            color: var(--text-body);
            font-size: 0.9rem;
        }
        .toggle-pills {
            display: flex;
            border: 1px solid #E2E8F0;
            border-radius: 50px;
            overflow: hidden;
        }
        .toggle-pills label {
            padding: 0.5rem 1.25rem;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-body);
            transition: all 0.3s ease;
        }
        .toggle-pills label.active {
            background: var(--primary-brand);
            color: white;
        }
        .toggle-pills input { display: none; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-row.full { grid-template-columns: 1fr; }
        .form-field label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-body);
            margin-bottom: 0.5rem;
        }
        .form-field input,
        .form-field select,
        .form-field textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            background: #fff;
            transition: all 0.3s ease;
        }
        .form-field input:focus,
        .form-field select:focus,
        .form-field textarea:focus {
            outline: none;
            border-color: var(--primary-brand);
            box-shadow: 0 0 0 3px rgba(35, 51, 106, 0.1);
        }
        .form-field textarea {
            min-height: 100px;
            resize: vertical;
        }
        .upload-box {
            border: 2px dashed #E2E8F0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-box:hover {
            border-color: var(--primary-brand);
            background: rgba(35, 51, 106, 0.02);
        }
        .upload-box i {
            font-size: 1.5rem;
            color: var(--text-muted);
            margin-right: 0.5rem;
        }
        .upload-box span {
            color: var(--text-body);
            font-size: 0.9rem;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
        }
        .btn-cancel {
            color: var(--text-body);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
        }
        .btn-submit {
            background: var(--primary-brand);
            color: white;
            padding: 0.875rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(35, 51, 106, 0.3);
        }
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .report-header { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 10rem; padding-bottom: 4rem;">
        <div class="report-form-card">
            <!-- Header -->
            <div class="report-header">
                <div>
                    <h2>Report an Item</h2>
                    <p>Help us reunite items with their owners.</p>
                </div>
                <div class="toggle-pills">
                    <input type="radio" name="type_toggle" id="toggle_lost" value="lost" checked>
                    <label for="toggle_lost" class="active" onclick="setType('lost')">Lost</label>
                    <input type="radio" name="type_toggle" id="toggle_found" value="found">
                    <label for="toggle_found" onclick="setType('found')">Found</label>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div style="background: var(--status-lost-bg); color: var(--status-lost-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="post_item.php" method="POST" enctype="multipart/form-data" id="reportForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="type" id="itemType" value="lost">
                
                <!-- Row 1: Item Name & Category -->
                <div class="form-row">
                    <div class="form-field">
                        <label>WHAT WAS IT?</label>
                        <input type="text" name="item_name" placeholder="Item Name (e.g. Blue Hydroflask)" required>
                    </div>
                    <div class="form-field">
                        <label>CATEGORY</label>
                        <select name="category" required>
                            <option value="">Electronics</option>
                            <?php foreach (ITEM_CATEGORIES as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Row 2: Date & Location -->
                <div class="form-row">
                    <div class="form-field">
                        <label>WHEN?</label>
                        <input type="date" name="date_lost" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-field">
                        <label>WHERE?</label>
                        <input type="text" name="location" placeholder="Specific Location" required>
                    </div>
                </div>
                
                <!-- Row 3: Description -->
                <div class="form-row full">
                    <div class="form-field">
                        <label>DESCRIPTION</label>
                        <textarea name="description" placeholder="Brief details..." required></textarea>
                    </div>
                </div>
                
                <!-- Row 4: Upload -->
                <div class="form-row full">
                    <label class="upload-box" for="imageUpload">
                        <i class="ri-image-add-line"></i>
                        <span id="uploadText">Upload Photo</span>
                        <input type="file" name="image" id="imageUpload" accept="image/*" style="display: none;">
                    </label>
                </div>
                
                <!-- Actions -->
                <div class="form-actions">
                    <a href="index.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit" id="submitBtn">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setType(type) {
            document.getElementById('itemType').value = type;
            document.querySelectorAll('.toggle-pills label').forEach(l => l.classList.remove('active'));
            document.querySelector('label[for="toggle_' + type + '"]').classList.add('active');
        }

        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File is too large. Maximum size is 2MB.');
                    this.value = '';
                    return;
                }
                document.getElementById('uploadText').textContent = file.name;
            }
        });

        document.getElementById('reportForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.textContent = 'Submitting...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

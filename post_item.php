<?php
// Validating and Mapping inputs to legacy handlers
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <div class="logo-icon">🏠</div>
                <div class="logo-text">
                    <span>EWU</span>
                    LOST &<br>FOUND
                </div>
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
            
            <form action="post_item.php" method="POST" enctype="multipart/form-data" id="reportForm">
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
                        <button type="button" class="ai-btn">✨ AI Auto-Fill</button>
                        <textarea name="description" class="form-control" placeholder="Brief details..."></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label class="upload-area" for="imageUpload">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">Upload Photo</div>
                            <input type="file" name="image" id="imageUpload" accept="image/*">
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
                    <button type="submit" class="btn-pill btn-lg">Submit Report</button>
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

        // Show selected file name
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                document.querySelector('.upload-text').textContent = fileName;
            }
        });
    </script>
</body>
</html>

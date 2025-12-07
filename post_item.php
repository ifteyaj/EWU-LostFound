<?php
// Validating and Mapping inputs to legacy handlers
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    
    // Common mappings
    if ($type == 'lost') {
        $_POST['last_location'] = $_POST['location'];
        $_POST['student_name'] = $_POST['contact_name'];
        $_POST['student_id'] = $_POST['contact_id'];
        // date_lost is already there
        include 'handlers/handle_lost.php';
    } else {
        $_POST['found_location'] = $_POST['location'];
        $_POST['finder_name'] = $_POST['contact_name'];
        $_POST['finder_id'] = $_POST['contact_id'];
        $_POST['date_found'] = $_POST['date_lost'];
        // found items table doesn't have a date column in the original schema, so we ignore date_lost
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
    <title>Post Item - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function toggleDateVisibility() {
            const type = document.querySelector('select[name="type"]').value;
            // Optional: Hide Date for Found items if irrelevant, but keeping it visible is fine/common
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php">EWU Lost&Found</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="lost.php">Latest Items</a></li>
                <!-- CTA -->
                <li><a href="post_item.php" class="btn-pill" style="background: var(--accent-red); color: white;">Cancel</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="glass-card form-center">
            <h2 style="text-align: center; margin-bottom: 2rem;">Post a New Item</h2>
            
            <form action="post_item.php" method="POST" enctype="multipart/form-data">
                
                <!-- Type Selection -->
                <div class="form-group">
                    <label>Report Type</label>
                    <select name="type" class="form-control" onchange="toggleDateVisibility()" required>
                        <option value="lost">I Lost Something</option>
                        <option value="found">I Found Something</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Item Title</label>
                    <input type="text" name="item_name" class="form-control" placeholder="e.g. Blue Macbook Air" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Books">Books & Stationery</option>
                        <option value="IDs">IDs & Cards</option>
                        <option value="Keys">Keys</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date (Approximate)</label>
                    <input type="date" name="date_lost" class="form-control" required> 
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Library 3rd Floor" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Provide as much detail as possible..."></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <!-- Contact Info (Required by DB) -->
                 <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0; padding-top: 1rem;">
                    <label style="margin-bottom: 1rem; display:block; color:white; font-weight:600;">Contact Details</label>
                    <div class="form-group">
                        <input type="text" name="contact_name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                         <input type="text" name="contact_id" class="form-control" placeholder="Student ID" required>
                    </div>
                    <div class="form-group">
                         <input type="email" name="email" class="form-control" placeholder="University Email" required>
                    </div>
                 </div>

                <button type="submit" class="btn-pill" style="width: 100%; border-radius: 12px;">Submit Report</button>
            </form>
        </div>
    </div>
</body>
</html>

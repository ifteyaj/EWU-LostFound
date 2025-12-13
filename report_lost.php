<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="form-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h2 style="font-size: 2rem;">Report a Lost Item</h2>
                <p style="color: var(--text-body); margin-top: 0.5rem; max-width: 400px; margin-left: auto; margin-right: auto;">Please fill out the details below to help us find your item.</p>
            </div>

            <form action="handlers/handle_lost.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" required placeholder="e.g. Blue Backpack">
                </div>

                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Books/Stationery">Books/Stationery</option>
                        <option value="IDs/Cards">IDs/Cards</option>
                        <option value="Keys">Keys</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="4" required placeholder="Describe the item (color, brand, distinguishing marks)..." style="resize: vertical;"></textarea>
                </div>

                <div class="form-group">
                    <label>Last Seen Location *</label>
                    <input type="text" name="last_location" required placeholder="e.g. Library, Room 101">
                </div>

                <div class="form-group">
                    <label>Date Lost *</label>
                    <input type="date" name="date_lost" required>
                </div>

                <div class="form-group">
                    <label>Upload Image</label>
                    <label class="upload-area">
                        <input type="file" name="image" id="item-image" accept="image/*" style="display: none;">
                        <span id="upload-text" style="color: var(--text-body); font-size: 0.9rem;">Click or Drag to Upload Photo</span>
                    </label>
                </div>

                <div style="border-top: 1px solid var(--border-light); margin: 2.5rem 0; padding-top: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Contact Information</h3>
                    
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="student_name" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Student ID *</label>
                            <input type="text" name="student_id" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-pill btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem; justify-content: center;">Submit Report</button>
            </form>
            
            <script>
                // Simple script to update upload text
                document.getElementById('item-image').addEventListener('change', function(e) {
                    var fileName = e.target.files[0].name;
                    document.getElementById('upload-text').textContent = "Selected: " + fileName;
                    document.getElementById('upload-text').style.color = "var(--primary-brand)";
                    document.getElementById('upload-text').style.fontWeight = "600";
                });
            </script>
        </div>
    </div>
</body>
</html>

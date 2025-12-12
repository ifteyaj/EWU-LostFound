<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item - EWU Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/img/logo.png" alt="Logo">
                    <span>EWU Lost & Found</span>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="lost.php">Lost Items</a></li>
                <li><a href="found.php">Found Items</a></li>
                <li><a href="report_lost.php" class="btn-primary">Report Lost</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">
        <div class="form-card">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <h2 style="font-size: 2rem;">Report a Found Item</h2>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Help return an item to its owner by filing a report.</p>
            </div>

            <form action="handlers/handle_found.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Item Name *</label>
                    <input type="text" name="item_name" required placeholder="e.g. Black Umbrella">
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
                    <textarea name="description" rows="4" required placeholder="Describe the item (color, brand, distinguishing marks)..."></textarea>
                </div>

                <div class="form-group">
                    <label>Found Location *</label>
                    <input type="text" name="found_location" required placeholder="e.g. Cafeteria, Table 5">
                </div>

                <div class="form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image" id="item-image" accept="image/*" style="padding: 0.5rem;">
                </div>

                <div style="border-top: 1px solid var(--border-light); margin: 2rem 0; padding-top: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Contact Information</h3>
                    
                    <div class="form-group">
                        <label>Finder Name *</label>
                        <input type="text" name="finder_name" required>
                    </div>

                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="finder_id" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Submit Report</button>
            </form>
        </div>
    </div>
</body>
</html>

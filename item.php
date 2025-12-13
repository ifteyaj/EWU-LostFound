<?php
/**
 * Item Detail Page
 * Displays details of a single lost or found item
 */
require_once 'init.php';

// Require login to view this page
requireLogin();

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$type = $_GET['type'];

// Validate type
if (!in_array($type, ['lost', 'found'])) {
    showError(404, "Invalid item type.");
}

$table = ($type == 'found') ? 'found_items' : 'lost_items';
$badge_class = ($type == 'found') ? 'status-found' : 'status-lost';
$badge_text = ($type == 'found') ? 'FOUND' : 'LOST';

if ($conn && !$conn->connect_error) {
    // Fetch item details
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
} else {
    $item = null;
}

if (!$item) {
    showError(404, "Item not found.");
}

// Handle Contact Form Submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    // Verify CSRF Token
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errorMessage = "Invalid security token. Please try again.";
    } else {
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        
        if (empty($subject) || empty($message)) {
            $errorMessage = "Please fill in all fields.";
        } else {
            // Fetch reporter email securely
            $reporterStmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ?");
            $reporterStmt->bind_param("i", $item['user_id']);
            $reporterStmt->execute();
            $reporterResult = $reporterStmt->get_result();
            
            if ($reporterResult->num_rows > 0) {
                $reporterData = $reporterResult->fetch_assoc();
                $to = $reporterData['email'];
                
                // Get current user info for "From" header (or logic)
                $currentUser = getCurrentUser();
                $fromEmail = $currentUser['email'];
                $fromName = getUserDisplayName();
                
                // Construct Email
                $emailSubject = "[EWU Lost&Found] $subject";
                $emailBody = "You have received a new message regarding your item: " . $item['item_name'] . "\n\n";
                $emailBody .= "From: $fromName ($fromEmail)\n\n";
                $emailBody .= "Message:\n$message\n\n";
                $emailBody .= "--------------------------------------------------\n";
                $emailBody .= "This email was sent via EWU Lost & Found Portal.";
                
                $headers = "From: no-reply@ewulostfound.com\r\n"; // Standard practice to send from system
                $headers .= "Reply-To: $fromEmail\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();
                
                // Send Email
                if (mail($to, $emailSubject, $emailBody, $headers)) {
                    $successMessage = "Message sent to the owner successfully!";
                } else {
                    $errorMessage = "Failed to send email. Please try again later. (Note: Localhost may not send emails)";
                }
            } else {
                $errorMessage = "Reporter not found.";
            }
        }
    }
}

$img_src = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : '';
$location = ($type == 'found') ? $item['found_location'] : $item['last_location'];
$event_date = ($type == 'found') ? $item['date_found'] : $item['date_lost'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> - EWU Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/modal.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="padding-top: 10rem; padding-bottom: 4rem;">
        <a href="javascript:history.back()" class="back-link">
            ← Back to Items
        </a>
        
        <!-- Messages -->
        <?php if ($successMessage): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid #a7f3d0;">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid #fecaca;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <div class="detail-card">
            <div class="detail-image">
                <?php if($img_src): ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-weight:500; font-size: 1.2rem;">No Image Available</div>
                <?php endif; ?>
                <span class="status-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
            </div>
            
            <div class="detail-info">
                <!-- Category & Date Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span style="color: var(--primary-brand); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($item['category']); ?></span>
                    <span style="color: #f97316; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9V3H15V1H17V3ZM4 9V19H20V9H4ZM6 11H8V13H6V11ZM11 11H13V13H11V11ZM16 11H18V13H16V11Z"></path></svg>
                        <?php echo date('d F, Y', strtotime($event_date)); ?>
                    </span>
                </div>
                
                <!-- Item Title -->
                <h1 class="detail-title"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                
                <!-- Location -->
                <div class="detail-section">
                    <div>
                        <div class="detail-label">Location</div>
                        <div class="detail-value" style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="color: var(--primary-brand); flex-shrink: 0;"><path d="M12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.14876 5.63604 4.63404C9.15076 1.11932 14.8513 1.11932 18.366 4.63404C21.8807 8.14876 21.8807 13.8492 18.366 17.364L12 23.7279ZM16.9518 15.9497C19.6855 13.2161 19.6855 8.78392 16.9518 6.05025C14.2181 3.31658 9.78596 3.31658 7.05228 6.05025C4.31861 8.78392 4.31861 13.2161 7.05228 15.9497L12 20.8975L16.9518 15.9497ZM12 13C10.8954 13 10 12.1046 10 11C10 9.89543 10.8954 9 12 9C13.1046 9 14 9.89543 14 11C14 12.1046 13.1046 13 12 13Z"></path></svg>
                            <?php echo htmlspecialchars($location); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="detail-section">
                    <div>
                        <div class="detail-label">Description</div>
                        <div class="detail-value"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
                    </div>
                </div>

                <!-- Actions -->
                <div style="margin-top: 2rem;">
                    <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 1rem;">ACTIONS</div>
                    <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
                        <?php 
                        $currentUserId = getCurrentUserId();
                        $isOwner = ($item['user_id'] == $currentUserId);
                        
                        if ($isOwner): 
                        ?>
                            <!-- Edit Report Button -->
                            <a href="edit_item.php?type=<?php echo $type; ?>&id=<?php echo $item['id']; ?>" class="btn-pill btn-primary" style="padding: 0.8rem 2rem;">
                                Edit Report
                            </a>
                            
                            <!-- Delete Link -->
                            <a href="handlers/manage_item.php?action=delete&type=<?php echo $type; ?>&id=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>" 
                               onclick="return confirm('Are you sure you want to delete this report?');"
                               style="color: var(--status-lost-text); font-weight: 600; font-size: 0.95rem;">
                                Delete
                            </a>
                        <?php else: ?>
                            <!-- Contact Button for Non-Owners -->
                            <button class="btn-pill btn-primary" style="padding: 0.8rem 2rem;" onclick="openContactModal()">
                                Contact <?php echo $type == 'found' ? 'Finder' : 'Reporter'; ?>
                            </button>
                        <?php endif; ?>
                        
                        <!-- Share Button -->
                        <button class="btn-pill" style="background: #F1F5F9; color: var(--text-head); border: none; padding: 0.8rem 2.5rem; margin-left: auto;" onclick="navigator.share ? navigator.share({title: '<?php echo htmlspecialchars($item['item_name']); ?>', url: window.location.href}) : navigator.clipboard.writeText(window.location.href).then(() => alert('Link copied!'))">
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeContactModal()">&times;</button>
            <div class="modal-header">
                <h3 class="modal-title">Contact <?php echo $type == 'found' ? 'Finder' : 'Reporter'; ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Send a message regarding <strong><?php echo htmlspecialchars($item['item_name']); ?></strong></p>
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <input type="hidden" name="send_message" value="1">
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-head);">Subject</label>
                    <input type="text" name="subject" class="search-input" value="Regarding: <?php echo htmlspecialchars($item['item_name']); ?>" required style="width: 100%; border: 1px solid var(--glass-border); border-radius: 0.5rem; padding: 0.8rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-head);">Message</label>
                    <textarea name="message" rows="5" required style="width: 100%; border: 1px solid var(--glass-border); border-radius: 0.5rem; padding: 0.8rem; font-family: inherit; resize: vertical;" placeholder="Hi, I saw your report..."></textarea>
                </div>
                
                <button type="submit" class="btn-pill btn-primary" style="width: 100%; justify-content: center;">Send Message</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function openContactModal() {
            document.getElementById('contactModal').classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Close modal when clicking outside
        document.getElementById('contactModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContactModal();
            }
        });
    </script>
</body>
</html>

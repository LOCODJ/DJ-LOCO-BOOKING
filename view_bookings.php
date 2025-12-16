<?php
// --- SECURITY SETTINGS ---
$valid_username = 'djloco_admin'; // CHANGE THIS
$valid_password = 'Y0urSuP3rS3cr3tP@ssw0rd'; // *** MUST CHANGE THIS PASSWORD ***
$file_path = 'booking_entries.csv'; // Must match the file path in save_booking.php
// -------------------------

session_start();

// 1. Check if user is trying to log in
if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === $valid_username && $_POST['password'] === $valid_password) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = "Invalid Username or Password.";
    }
}

// 2. If not authenticated, show login form
if (!isset($_SESSION['authenticated'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Admin Login</title><link rel="stylesheet" href="style.css"></head>
<body style="background-color:#000; text-align:center; padding-top:100px;">
    <div style="background:#2b2b2b; width:350px; margin:0 auto; padding:30px; border-radius:10px;">
        <h2 style="color:#fff;">DJ LOCO Admin Login</h2>
        <?php if (isset($error)) { echo '<p style="color:red;">' . $error . '</p>'; } ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="padding:10px; margin-bottom:10px; width:100%;">
            <input type="password" name="password" placeholder="Password" required style="padding:10px; margin-bottom:20px; width:100%;">
            <button type="submit" style="padding:10px; background:#007bff; color:#fff; border:none; border-radius:5px; width:100%;">Log In</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}

// 3. If authenticated, show the bookings
// Read the CSV file
if (file_exists($file_path) && $csv_data = file($file_path)) {
    // Reverse the array to show the newest booking at the top
    $csv_data = array_reverse($csv_data); 
} else {
    $csv_data = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookings Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1f1f1f; color: #fff; padding: 20px; }
        h1 { color: #00bcd4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background-color: #333; color: #fff; }
        tr:nth-child(even) { background-color: #2b2b2b; }
        .logout { float: right; color: #fff; text-decoration: none; background: #dc3545; padding: 5px 10px; border-radius: 5px;}
    </style>
</head>
<body>
    <h1>DJ LOCO Booking Entries <a href="?logout=true" class="logout">Log Out</a></h1>

    <?php if (empty($csv_data)) : ?>
        <p>No booking requests have been submitted yet.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Submitted</th>
                    <th>Name</th>
                    <th>Date & Time</th>
                    <th>Venue</th>
                    <th>Type</th>
                    <th>Details/Requests</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($csv_data as $line) : 
                    // Use str_getcsv to correctly parse comma-separated values
                    $data = str_getcsv($line);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($data[0] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($data[1] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($data[2] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($data[3] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($data[4] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($data[5] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_bookings.php');
    exit;
}
?>
<?php
// Define the file path where booking entries will be saved (must be writable by the server)
$file_path = 'booking_entries.csv';

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. COLLECT AND SANITIZE FORM DATA
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $dateTime = filter_var($_POST['event_date_time'], FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['venue_location'], FILTER_SANITIZE_STRING);
    $eventType = filter_var($_POST['event_type'], FILTER_SANITIZE_STRING);
    $details = filter_var($_POST['additional_details'], FILTER_SANITIZE_STRING);
    $submissionTime = date('Y-m-d H:i:s'); // Record when the booking was submitted

    // Basic validation
    if (empty($name) || empty($dateTime) || empty($location) || empty($eventType)) {
        header('Location: index.html?status=error_missing_fields');
        exit;
    }

    // 2. FORMAT THE DATA
    // Prepare the data as a comma-separated line (CSV format)
    // Note: str_replace is used to ensure commas in user input don't break the CSV structure.
    $data_array = [
        $submissionTime,
        str_replace(',', ' ', $name),
        str_replace('T', ' ', $dateTime),
        str_replace(',', ' ', $location),
        str_replace(',', ' ', $eventType),
        str_replace([',', "\n", "\r"], [' ', ' ', ' '], $details) // Clean up details
    ];

    $entry = implode(',', $data_array) . "\n";

    // 3. SAVE THE DATA
    // FILE_APPEND adds the data to the end of the file. LOCK_EX prevents others from writing at the same time.
    if (file_put_contents($file_path, $entry, FILE_APPEND | LOCK_EX) !== false) {
        // Success
        header('Location: index.html?status=success_saved');
    } else {
        // Failure
        header('Location: index.html?status=error_save_failed');
    }

} else {
    header('Location: index.html');
    exit;
}
?>
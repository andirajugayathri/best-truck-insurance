<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Start output buffering
ob_start();

// Check if db.php exists
if (!file_exists('db.php')) {
    error_log("Error: db.php file not found in " . __DIR__);
    http_response_code(500);
    echo "Server configuration error. Please contact the administrator.";
    exit;
}

include 'db.php';

// Initialize variables
$fromDate = isset($_GET['from_date']) && !empty($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$toDate = isset($_GET['to_date']) && !empty($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$leads = [];
$errorMessage = '';

// Check if truck_insurance table and created_at column exist
try {
    if (!isset($pdo)) {
        throw new Exception("Database connection not established: \$pdo is not defined.");
    }
    
    // Check if table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'truck_insurance'");
    if ($tableCheck->rowCount() === 0) {
        throw new Exception("Table 'truck_insurance' does not exist in the database.");
    }
    
    // Check if created_at column exists
    $columnCheck = $pdo->query("SHOW COLUMNS FROM truck_insurance LIKE 'created_at'");
    $hasCreatedAt = $columnCheck->rowCount() > 0;
    
    // Fetch leads
    if ($hasCreatedAt) {
        $sql = "SELECT * FROM truck_insurance WHERE created_at BETWEEN :from_date AND :to_date ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':from_date' => $fromDate,
            ':to_date' => $toDate . ' 23:59:59'
        ]);
    } else {
        error_log("Warning: created_at column not found in truck_insurance. Fetching all records.");
        $sql = "SELECT * FROM truck_insurance ORDER BY id DESC";
        $stmt = $pdo->query($sql);
    }
    
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    $errorMessage = "Unable to fetch leads: " . htmlspecialchars($e->getMessage());
}

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    try {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="truck_insurance_leads_' . date('Ymd') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        $headers = [
            'Full Name', 'Address', 'Contact Number', 'Email', 'Company Name', 'Occupation', 
            'Renewal Date', 'Vehicle Type', 'Carrying Capacity', 'Trailer Cover Required', 
            'Truck Details', 'Base Operation', 'Public Liability Cover', 'Sum Insured', 
            'Radius Operation', 'Marine Cover', 'Registration Number', 'Type of Goods Carried', 
            'Require Finance', 'Business Established', 'Current Insurer', 'Driver Age', 
            'Years Continuously Insured', 'Truck Insurance Claims', 'Years Truck Licence Held', 
            'Driving Convictions', 'Created At'
        ];
        fputcsv($output, $headers);

        // Write data
        foreach ($leads as $lead) {
            $row = [
                $lead['full_name'] ?? '',
                $lead['address'] ?? '',
                $lead['contact_number'] ?? '',
                $lead['email'] ?? '',
                $lead['company_name'] ?? '',
                $lead['occupation'] ?? '',
                $lead['renewal_date'] ?? '',
                $lead['vehicle_type'] ?? '',
                $lead['carrying_capacity'] ?? '',
                $lead['trailer_cover_required'] ?? '',
                $lead['truck_details'] ?? '',
                $lead['base_operation'] ?? '',
                $lead['public_liability_cover'] ?? '',
                $lead['sum_insured'] ?? '',
                $lead['radius_operation'] ?? '',
                $lead['marine_cover'] ?? '',
                $lead['registration_number'] ?? '',
                $lead['type_goods_carried'] ?? '',
                $lead['require_finance'] ?? '',
                $lead['business_established'] ?? '',
                $lead['current_insurer'] ?? '',
                $lead['driver_age'] ?? '',
                $lead['year_continuously_insured'] ?? '',
                $lead['number_truck_insurance'] ?? '',
                $lead['year_truck_licence_held'] ?? '',
                $lead['driving_convictions'] ?? '',
                $lead['created_at'] ?? ''
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        ob_end_flush();
        exit;
    } catch (Exception $e) {
        error_log("CSV Export Error: " . $e->getMessage());
        $errorMessage = "Failed to export to CSV: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truck Insurance Leads</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .filter-form { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .export-btn { margin: 10px 0; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Truck Insurance Leads</h2>
    
    <?php if ($errorMessage): ?>
        <div class="error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <!-- Date Range Filter Form -->
    <form class="filter-form" method="GET">
        <label for="from_date">From Date:</label>
        <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
        <label for="to_date">To Date:</label>
        <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
        <button type="submit">Filter</button>
    </form>
    
    <!-- Export Button -->
    <form class="export-btn" method="GET">
        <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
        <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
        <input type="hidden" name="export" value="csv">
        <button type="submit">Export to CSV</button>
    </form>
    
    <!-- Leads Table -->
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Vehicle Type</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leads)): ?>
                <tr><td colspan="6">No leads found for the selected date range.</td></tr>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($lead['full_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($lead['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($lead['contact_number'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($lead['company_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($lead['vehicle_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($lead['created_at'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php ob_end_flush(); ?>
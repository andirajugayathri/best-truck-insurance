<?php
// Start output buffering
ob_start();

// Including DB Connection & Access Token File
include 'db.php'; 
include '../access-token.php';

/**
 * Step 1: Collecting the Form Fields 
 * Step 2: Inserting into DB (btf_quotes)
 * Step 3: Sending data to Zoho CRM
 */

// Mapping form fields to match Zoho CRM
function mapFormFields($formData) {
    return [
        'Last_Name' => $formData['full-name'] ?? '',
        'Email' => $formData['email'] ?? '',
        'Phone' => $formData['contact-number'] ?? '',
        'Loan_Amount' => $formData['loan-amount'] ?? '',
        'Layout'=> [
            'name'=> 'Website',
            'id' => '62950000001318018'
        ],
         'Owner'=>[
                'name'=> 'Shalin Shah',
            'id'=> '62950000000229001'
            
                   ],
        'Enquiry_Source' => 'Best Truck Insurance Website - Request a Quote',
'Product_Inquiry' => 'Truck & Trailer Insurance (Single)'
    ];
}

// Function to insert data into the database
function insertDataIntoDatabase($mappedData, $pdo) {
    try {
        $sql = "INSERT INTO btf_quotes (
                    full_name, email, contact_number, loan_amount, submission_date
                ) VALUES (
                    :full_name, :email, :contact_number, :loan_amount, NOW()
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $mappedData['Last_Name'],
            ':email' => $mappedData['Email'],
            ':contact_number' => $mappedData['Phone'],
            ':loan_amount' => $mappedData['Loan_Amount']
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Function to send data to Zoho CRM
function addRecordToZoho($mappedData, $pdo) {
    getAccessToken($pdo);
    $accessToken = $_SESSION['access_token'] ?? null;
    if (!$accessToken) {
        error_log("Error: Missing Zoho access token.");
        return false;
    }
    
    $module = 'Leads';
    $apiUrl = "https://www.zohoapis.com.au/crm/v2/$module";
    $data = ['data' => [$mappedData]];
    $jsonData = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Zoho-oauthtoken $accessToken",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 201) {
        return true;
    } else {
        error_log("Zoho API Error ({$httpCode}): " . json_encode($response));
        return false;
    }
}

// Main logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mappedData = mapFormFields($_POST);
    
    // Basic validation
    if (empty($mappedData['Last_Name']) || empty($mappedData['Email']) || empty($mappedData['Phone'])) {
        header("Location: https://besttruckinsurance.com.au/thankyou.html");
        exit;
    }
    
    // Email notification
    $to = "info@ilinkinsurance.com.au, smartsolutions.designstudio@gmail.com, quotes@ilinkinsurance.com.au";
    $subject = "New BTF Loan Quote Request";
    $message = "
    <html>
    <head>
      <title>New BTF Loan Quote Request</title>
      <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
      </style>
    </head>
    <body>
      <h2>New BTF Loan Quote Request</h2>
      <table>
        <tr><th>Full Name</th><td>{$mappedData['Last_Name']}</td></tr>
        <tr><th>Email</th><td>{$mappedData['Email']}</td></tr>
        <tr><th>Phone Number</th><td>{$mappedData['Phone']}</td></tr>
        <tr><th>Loan Amount</th><td>{$mappedData['Loan_Amount']}</td></tr>
      </table>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8\r\n";
              $headers .= "From: Best Truck Insurance <mail@besttruckinsurance.com.au>\r\n";
            $headers .= "Reply-To: $userEmail\r\n";
    $headers .= "Reply-To: {$mappedData['Email']}\r\n";

    // Sending the email
    mail($to, $subject, $message, $headers);
    
    // Save to database and send to Zoho CRM
    if (insertDataIntoDatabase($mappedData, $pdo) && addRecordToZoho($mappedData, $pdo)) {
        header("Location:  https://besttruckinsurance.com.au/thankyou.html");
        exit;
    } else {
        header("Location:  https://besttruckinsurance.com.au/thankyou.html");
        exit;
    }
}
?>
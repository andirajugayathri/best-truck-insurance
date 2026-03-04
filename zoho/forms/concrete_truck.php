<?php
ob_start();
session_start();

include 'db.php';
include '../access-token.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/concrete_truck_error.log');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Map Form Fields
|--------------------------------------------------------------------------
*/
function mapFormFields($formData)
{
    $coverageOptions = isset($formData['coverage_options']) && is_array($formData['coverage_options'])
        ? implode(', ', $formData['coverage_options'])
        : '';

    return [
        'Last_Name' => trim($formData['full_name'] ?? ''),
        'Address_Line1' => trim($formData['address'] ?? ''),
        'Phone' => trim($formData['contact_number'] ?? ''),
        'Email' => trim($formData['email'] ?? ''),
        'Company' => trim($formData['company_name'] ?? ''),
        'Sum_Insured' => trim($formData['truck_details'] ?? ''),

        // ✅ Correct CRM field names
        'Type_of_Coverage_required' => $coverageOptions,
        'Additional_Information_or_Coverage_Request' => trim($formData['additional_info'] ?? ''),

        // Zoho system fields
        'Product_Inquiry' => 'Concrete Truck Insurance',
        'Sales_Team' => 'Shalin Shah - AR: 418137',
        'Service_Team' => 'Shalin Shah',
        'Layout' => [
            'name' => 'Website',
            'id' => '62950000001318018'
        ],
        'Owner' => [
            'name' => 'Shalin Shah',
            'id' => '62950000000229001',
            'email' => 'shalin@ilinkinsurance.com.au'
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| Insert Into Database
|--------------------------------------------------------------------------
*/
function insertDataIntoDatabase($data, $pdo)
{
    try {
        $sql = "INSERT INTO concrete_truck (
            full_name,
            address,
            contact_number,
            email,
            company_name,
            sum_insured,
            coverage_options,
            additional_info
        ) VALUES (
            :full_name,
            :address,
            :contact_number,
            :email,
            :company_name,
            :sum_insured,
            :coverage_options,
            :additional_info
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['Last_Name'],
            ':address' => $data['Address_Line1'],
            ':contact_number' => $data['Phone'],
            ':email' => $data['Email'],
            ':company_name' => $data['Company'],
            ':sum_insured' => $data['Sum_Insured'],
            ':coverage_options' => $data['Type_of_Coverage_required'],
            ':additional_info' => $data['Additional_Information_or_Coverage_Request']
        ]);

        return true;
    } catch (PDOException $e) {
        error_log('DB Error: ' . $e->getMessage());
        return false;
    }
}

/*
|--------------------------------------------------------------------------
| Send Lead to Zoho CRM (STRICT)
|--------------------------------------------------------------------------
*/
function addRecordToZoho($data, $pdo)
{
    getAccessToken($pdo);
    $accessToken = $_SESSION['access_token'] ?? null;

    if (!$accessToken) {
        error_log('Zoho Error: Access token missing');
        return false;
    }

    $payload = json_encode(['data' => [$data]]);

    $ch = curl_init("https://www.zohoapis.com.au/crm/v2/Leads");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Zoho-oauthtoken $accessToken",
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 201) {
        error_log("Zoho HTTP Error ($httpCode): $response");
        return false;
    }

    $decoded = json_decode($response, true);

    if (isset($decoded['data'][0]['status']) && $decoded['data'][0]['status'] === 'success') {
        return true;
    }

    error_log("Zoho API Error: $response");
    return false;
}

/*
|--------------------------------------------------------------------------
| Send Email (AFTER SUCCESS)
|--------------------------------------------------------------------------
*/
function sendNotificationEmail($data)
{
    $safe = fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

    $to = "info@ilinkinsurance.com.au, quotes@ilinkinsurance.com.au, smartsolutions.designstudio@gmail.com, smartsolutions.designstudio1@gmail.com";
    $subject = "New Submission – Concrete Truck Insurance";

    $message = "
    <html><body>
    <h2>New Concrete Truck Insurance Inquiry</h2>
    <table border='1' cellpadding='8' cellspacing='0' width='100%'>
        <tr><th>Full Name</th><td>{$safe($data['Last_Name'])}</td></tr>
        <tr><th>Company</th><td>{$safe($data['Company'])}</td></tr>
        <tr><th>Email</th><td>{$safe($data['Email'])}</td></tr>
        <tr><th>Phone</th><td>{$safe($data['Phone'])}</td></tr>
        <tr><th>Address</th><td>{$safe($data['Address_Line1'])}</td></tr>
        <tr><th>Sum Insured</th><td>{$safe($data['Sum_Insured'])}</td></tr>
        <tr><th>Coverage</th><td>{$safe($data['Type_of_Coverage_required'])}</td></tr>
        <tr><th>Additional Info</th><td>{$safe($data['Additional_Information_or_Coverage_Request'])}</td></tr>
    </table>
    </body></html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Best Truck Insurance <mail@besttruckinsurance.com.au>\r\n";

    return mail($to, $subject, $message, $headers);
}

/*
|--------------------------------------------------------------------------
| Main Logic
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mappedData = mapFormFields($_POST);

    if (
        empty($mappedData['Last_Name']) ||
        empty($mappedData['Email']) ||
        empty($mappedData['Phone'])
    ) {
        header("Location: /error.html");
        exit;
    }

    if (!insertDataIntoDatabase($mappedData, $pdo)) {
        header("Location: /error.html");
        exit;
    }

    if (!addRecordToZoho($mappedData, $pdo)) {
        header("Location: /error.html");
        exit;
    }

    sendNotificationEmail($mappedData);

    ob_end_clean();
    header("Location: https://besttruckinsurance.com.au/thankyou.html");
    exit;
}

header("Location: /error.html");
exit;

<?php
ob_start();
session_start();

include 'db.php';
include '../access-token.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/horse_truck_error.log');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| STEP 1: MAP FORM FIELDS
|--------------------------------------------------------------------------
*/
function mapFormFields($formData)
{
    $coverageOptions = isset($formData['coverage_options']) && is_array($formData['coverage_options'])
        ? implode(', ', $formData['coverage_options'])
        : '';

    return [
        'Last_Name' => trim($formData['full_name'] ?? ''),
        'Phone' => trim($formData['contact_number'] ?? ''),
        'Email' => trim($formData['email'] ?? ''),
        'Company' => trim($formData['company_name'] ?? ''),
        'Street' => trim($formData['address'] ?? ''),
        'Sum_Insured' => trim($formData['truck_details'] ?? ''),

        // ✅ Correct CRM field names
        'Type_of_Coverage_required' => $coverageOptions,
        'Additional_Information_or_Coverage_Request' => trim($formData['additional_info'] ?? ''),

        // Zoho CRM fixed fields
        'Product_Inquiry' => 'Horse Truck Insurance',
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
| STEP 2: INSERT INTO DATABASE
|--------------------------------------------------------------------------
*/
function insertDataIntoDatabase($data, $pdo)
{
    try {
        $sql = "INSERT INTO horse_truck (
            full_name,
            company_name,
            contact_number,
            email,
            address,
            coverage_options,
            additional_info,
            sum_insured
        ) VALUES (
            :full_name,
            :company_name,
            :contact_number,
            :email,
            :address,
            :coverage_options,
            :additional_info,
            :sum_insured
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['Last_Name'],
            ':company_name' => $data['Company'],
            ':contact_number' => $data['Phone'],
            ':email' => $data['Email'],
            ':address' => $data['Street'],
            ':coverage_options' => $data['Type_of_Coverage_required'],
            ':additional_info' => $data['Additional_Information_or_Coverage_Request'],
            ':sum_insured' => $data['Sum_Insured']
        ]);

        return true;

    } catch (PDOException $e) {
        error_log("DB ERROR: " . $e->getMessage());
        error_log("DATA: " . json_encode($data));
        return false;
    }
}

/*
|--------------------------------------------------------------------------
| STEP 3: SEND TO ZOHO (STRICT)
|--------------------------------------------------------------------------
*/
function addRecordToZoho($data, $pdo)
{
    getAccessToken($pdo);
    $accessToken = $_SESSION['access_token'] ?? null;

    if (!$accessToken) {
        error_log("ZOHO ERROR: Access token missing");
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
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 201) {
        error_log("ZOHO ERROR ($status): $response");
        return false;
    }

    $decoded = json_decode($response, true);

    return isset($decoded['data'][0]['status']) &&
           $decoded['data'][0]['status'] === 'success';
}

/*
|--------------------------------------------------------------------------
| STEP 4: EMAIL (AFTER SUCCESS)
|--------------------------------------------------------------------------
*/
function sendNotificationEmail($data)
{
    $safe = fn($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

    $to = "info@ilinkinsurance.com.au,quotes@ilinkinsurance.com.au,gayathriandiraju@gmail.com";
    $subject = "New Horse Truck Insurance Quote Request";

    $message = "
    <html><body>
    <h2>New Horse Truck Insurance Quote</h2>
    <table border='1' cellpadding='10' cellspacing='0'>
        <tr><th>Full Name</th><td>{$safe($data['Last_Name'])}</td></tr>
        <tr><th>Company</th><td>{$safe($data['Company'])}</td></tr>
        <tr><th>Phone</th><td>{$safe($data['Phone'])}</td></tr>
        <tr><th>Email</th><td>{$safe($data['Email'])}</td></tr>
        <tr><th>Address</th><td>{$safe($data['Street'])}</td></tr>
        <tr><th>Sum Insured</th><td>{$safe($data['Sum_Insured'])}</td></tr>
        <tr><th>Coverage</th><td>{$safe($data['Type_of_Coverage_required'])}</td></tr>
        <tr><th>Additional Info</th><td>{$safe($data['Additional_Information_or_Coverage_Request'])}</td></tr>
    </table>
    </body></html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Horse Truck Insurance <mail@besttruckinsurance.com.au>\r\n";

    return mail($to, $subject, $message, $headers);
}

/*
|--------------------------------------------------------------------------
| MAIN EXECUTION
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mappedData = mapFormFields($_POST);

    if (
        empty($mappedData['Last_Name']) ||
        empty($mappedData['Phone']) ||
        empty($mappedData['Email'])
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

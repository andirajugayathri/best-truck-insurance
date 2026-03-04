<?php
// Start session and include DB connection
session_start();
include 'forms/db.php';

// OAuth credentials (consider loading from environment variables for security)
$client_id = '1000.BH3GSQ5BHK6QJSRO8Z39P258BGNSWW';
$client_secret = '39f7052dbbed08270a98341661cbbc9ab83532b968';
$redirect_uri = 'https://besttruckinsurance.com.au/zoho/authcode.php';
$grant_token = '1000.cda5368621884f9c72cc97b63cd7e2e5.760645bbd3275ca2ec66f87ef0590431';

// Token endpoint
$token_url = "https://accounts.zoho.com.au/oauth/v2/token";

// Build POST fields
$post_fields = http_build_query([
    'grant_type' => 'authorization_code',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'code' => $grant_token,
]);

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);

// Check for cURL error
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Decode response
$tokens = json_decode($response, true);

// Handle errors
if (isset($tokens['error'])) {
    echo "Error: " . htmlspecialchars($tokens['error_description'] ?? $tokens['error']);
    exit;
}

// Ensure access and refresh tokens are present
if (!isset($tokens['access_token'], $tokens['refresh_token'])) {
    echo "Failed to receive tokens. Response: " . htmlspecialchars($response);
    exit;
}

// Calculate expiry timestamp (optional but helpful)
$expiry_time = time() + $tokens['expires_in'];

// Insert tokens into database
try {
    $sql = "INSERT INTO oauthtoken (client_id, refresh_token, access_token, grant_token, expiry_time) 
            VALUES (:client_id, :refresh_token, :access_token, :grant_token, :expiry_time)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'client_id' => $client_id,
        'refresh_token' => $tokens['refresh_token'],
        'access_token' => $tokens['access_token'],
        'grant_token' => $grant_token,
        'expiry_time' => $expiry_time
    ]);

    echo "Token saved successfully.";
} catch (PDOException $e) {
    echo "Database Error: " . htmlspecialchars($e->getMessage());
}
?>

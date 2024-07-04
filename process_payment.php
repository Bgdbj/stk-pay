<?php
// M-Pesa API credentials
$consumerKey = 'Bso3VEoupRGLev1GDD6li7jDyxaXWlE6rj9SVUlsxBz8wzwK';
$consumerSecret = 'sai0d32MihqGTAmNQVbBGfiYMTB5atTbcAH3xSqqCTUiGxrux3xuQxfr8uRkUsdF';
$passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$shortCode = '174379'; // M-Pesa short code
$callbackURL = 'https://scenic-sequoia-37866-19e32059d7e6.herokuapp.com/';

header('Content-Type: application/json');

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);

$amount = $data['amount'];
$phone = $data['phone'];

// Obtain OAuth token
$authUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$authCredentials = base64_encode("$consumerKey:$consumerSecret");

$ch = curl_init($authUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $authCredentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$authResponse = json_decode($response);
$accessToken = $authResponse->access_token;

// Generate timestamp
$timestamp = date('YmdHis');

// Generate password
$password = base64_encode($shortCode . $passKey . $timestamp);

// Prepare the payload
$payload = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackURL,
    'AccountReference' => 'InnovateHub',
    'TransactionDesc' => 'Pay',
];

// Initiate M-Pesa STK Push
$stkPushUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$ch = curl_init($stkPushUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
curl_close($ch);

$response = json_decode($response);

if ($response->ResponseCode === '0') {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $response->errorMessage]);
}
?>

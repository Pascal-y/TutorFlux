<?php
// Include the necessary files for generating access token and making payment requests
include "createaccesstoken.php";
include "createapiuser.php";

// Get form data from the payment form
$phone = $_POST['phone_number'];
$amount = $_POST['amount'];
$reason = $_POST['reason'];

// Generate a new UUID for the transaction
$reference_id = generate_uuid();

// Set the request URL for payment
$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay";

// Set the headers
$headers = array(
    'Authorization: Bearer ' . $access_token,
    'X-Reference-Id: ' . $reference_id,
    'X-Target-Environment: sandbox',
    'Content-Type: application/json',
    'Ocp-Apim-Subscription-Key: ' . $secodary_key
);

// Generate an external ID (8 digits)
$external_id = rand(10000000, 99999999);

// Set the request body
$body = array(
    'amount' => $amount,
    'currency' => 'XAF', // assuming XAF as the currency
    "externalId" => $external_id,
    'payer' => array(
        'partyIdType' => 'MSISDN',
        'partyId' => $phone
    ),
    'payerMessage' => $reason,
    'payeeNote' => 'Thank you for using TutorFlux Booking MTN Payment'
);

// Encode the request body as JSON
$json_body = json_encode($body);

// Initialize cURL
$curl = curl_init();

// Set the cURL options
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $json_body
));

// Execute the cURL request
$response = curl_exec($curl);

// Check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    echo "cURL Error: " . $error_msg;
} else {
    // Get HTTP status code
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Close the cURL session
    curl_close($curl);

    // Output the response status
    if ($httpcode == 202) {
        echo 'Payment request sent successfully. Reference ID: ' . $reference_id;
        echo 'Response status code: ' . $httpcode;
    } else {
        echo 'Payment request failed. Response status code: ' . $httpcode;
        echo "<br>";
        echo "Error: " . $response;
    }
}
?>

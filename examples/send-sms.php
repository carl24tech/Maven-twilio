<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TwilioSMS\Client;

// Configuration
$accountSid = getenv('TWILIO_ACCOUNT_SID') ?: 'your_account_sid';
$authToken = getenv('TWILIO_AUTH_TOKEN') ?: 'your_auth_token';

// Initialize client
$client = new Client($accountSid, $authToken, [
    'default_from_number' => '+1234567890', // Your Twilio phone number
    'timeout' => 30,
    'retry_attempts' => 3
]);

try {
    // Send SMS
    $response = $client->sms()->send(
        '+19876543210',  // Recipient number (E.164 format)
        'Hello from Twilio PHP SDK!'  // Message body
    );
    
    echo "SMS sent successfully!\n";
    echo "Message SID: " . $response->getSid() . "\n";
    echo "Status: " . $response->getStatus() . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

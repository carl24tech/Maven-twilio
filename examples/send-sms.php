<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TwilioSMS\Client;
use TwilioSMS\Model\SMSMessage;
use TwilioSMS\Exception\TwilioException;

// Configuration with proper validation
$accountSid = getenv('TWILIO_ACCOUNT_SID');
$authToken = getenv('TWILIO_AUTH_TOKEN');
$defaultFromNumber = getenv('TWILIO_FROM_NUMBER') ?: '+1234567890';

// Validate credentials
if (!$accountSid || !$authToken) {
    die("Error: Missing Twilio credentials. Please set:\n" .
        "  TWILIO_ACCOUNT_SID\n" .
        "  TWILIO_AUTH_TOKEN\n");
}

// Initialize client (without unsupported config array)
$client = new Client($accountSid, $authToken);

// Define message details
$recipientNumber = '+19876543210';
$messageBody = 'Hello from Twilio PHP SDK!';

try {
    // Validate inputs
    if (empty($recipientNumber) || empty($messageBody)) {
        throw new \InvalidArgumentException("Recipient and message body are required");
    }
    
    // Create message object
    $smsMessage = new SMSMessage($recipientNumber, $messageBody, $defaultFromNumber);
    
    // Send SMS
    $response = $client->sms()->sendMessage($smsMessage);
    
    echo "✓ SMS sent successfully!\n";
    echo "  Message SID: " . $response->getSid() . "\n";
    echo "  Status: " . $response->getStatus() . "\n";
    echo "  To: " . $response->getTo() . "\n";
    echo "  From: " . $response->getFrom() . "\n";
    
    if ($response->getPrice()) {
        echo "  Cost: {$response->getPrice()} {$response->getPriceUnit()}\n";
    }
    
} catch (TwilioException $e) {
    echo "✗ Twilio API Error: " . $e->getMessage() . "\n";
    echo "  Code: " . $e->getCode() . "\n";
} catch (\InvalidArgumentException $e) {
    echo "✗ Validation Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "✗ Unexpected Error: " . $e->getMessage() . "\n";
}

<?php

namespace TwilioSMS\Service;

use Twilio\Rest\Client as TwilioRestClient;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use TwilioSMS\Config\TwilioConfig;
use TwilioSMS\Exception\TwilioException;
use TwilioSMS\Model\SMSMessage;
use TwilioSMS\Model\SMSResponse;
use Psr\Log\LoggerInterface;

class SMSService
{
    private TwilioRestClient $twilioClient;
    private TwilioConfig $config;
    private LoggerInterface $logger;

    public function __construct(
        TwilioRestClient $twilioClient,
        TwilioConfig $config,
        LoggerInterface $logger
    ) {
        $this->twilioClient = $twilioClient;
        $this->config = $config;
        $this->logger = $logger;
    }


    public function send(string $to, string $body, ?string $from = null, array $options = []): SMSResponse
    {
        $message = new SMSMessage($to, $body, $from ?? $this->config->getDefaultFromNumber());
        
        if (!$message->getFrom()) {
            throw new TwilioException("From number is required. Set default_from_number in config or provide it as parameter.");
        }

        $this->logger->info("Sending SMS", [
            'to' => $message->getTo(),
            'from' => $message->getFrom(),
            'body_length' => strlen($message->getBody())
        ]);

        try {
            $twilioMessage = $this->sendWithRetry($message, $options);
            $response = SMSResponse::fromTwilioMessage($twilioMessage);
            
            $this->logger->info("SMS sent successfully", [
                'sid' => $response->getSid(),
                'status' => $response->getStatus()
            ]);
            
            return $response;
        } catch (\Exception $e) {
            $this->logger->error("Failed to send SMS", [
                'to' => $message->getTo(),
                'error' => $e->getMessage()
            ]);
            throw new TwilioException("Failed to send SMS: " . $e->getMessage(), 0, $e);
        }
    }


    public function sendMultiple(array $messages): array
    {
        $responses = [];
        
        foreach ($messages as $messageData) {
            try {
                $to = $messageData['to'] ?? null;
                $body = $messageData['body'] ?? null;
                $from = $messageData['from'] ?? null;
                
                if (!$to || !$body) {
                    throw new TwilioException("Each message must have 'to' and 'body' fields");
                }
                
                $responses[] = $this->send($to, $body, $from);
            } catch (TwilioException $e) {
                $this->logger->error("Failed to send individual message", [
                    'to' => $to ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                $responses[] = null;
            }
        }
        
        return $responses;
    }


    public function getMessage(string $messageSid): SMSResponse
    {
        try {
            $message = $this->twilioClient->messages($messageSid)->fetch();
            return SMSResponse::fromTwilioMessage($message);
        } catch (\Exception $e) {
            throw new TwilioException("Failed to fetch message: " . $e->getMessage());
        }
    }

    private function sendWithRetry(SMSMessage $message, array $options = []): MessageInstance
    {
        $attempts = 0;
        $maxAttempts = $this->config->getRetryAttempts();
        $lastException = null;
        
        while ($attempts < $maxAttempts) {
            try {
                return $this->twilioClient->messages->create(
                    $message->getTo(),
                    [
                        'from' => $message->getFrom(),
                        'body' => $message->getBody(),
                        'statusCallback' => $options['status_callback'] ?? null,
                        'provideFeedback' => $options['provide_feedback'] ?? false,
                        'validityPeriod' => $options['validity_period'] ?? null
                    ]
                );
            } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;
                
                if ($attempts < $maxAttempts) {
                    $this->logger->warning("Retry attempt {$attempts} for SMS", [
                        'to' => $message->getTo(),
                        'error' => $e->getMessage()
                    ]);
                    usleep(1000000 * $attempts); // Exponential backoff
                }
            }
        }
        
        throw $lastException;
    }
}

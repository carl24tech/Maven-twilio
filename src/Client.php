<?php

namespace TwilioSMS;

use Twilio\Rest\Client as TwilioRestClient;
use TwilioSMS\Config\TwilioConfig;
use TwilioSMS\Exception\TwilioException;
use TwilioSMS\Service\SMSService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Client
{
    private TwilioRestClient $twilioClient;
    private TwilioConfig $config;
    private LoggerInterface $logger;
    private SMSService $smsService;


    public function __construct(string $accountSid, string $authToken, array $options = [])
    {
        $this->config = new TwilioConfig($accountSid, $authToken, $options);
        
        try {
            $this->twilioClient = new TwilioRestClient($accountSid, $authToken);
        } catch (\Exception $e) {
            throw new TwilioException("Failed to initialize Twilio client: " . $e->getMessage());
        }
        
        $this->logger = $options['logger'] ?? new NullLogger();
        $this->smsService = new SMSService($this->twilioClient, $this->config, $this->logger);
    }


    public function sms(): SMSService
    {
        return $this->smsService;
    }


    public function getTwilioClient(): TwilioRestClient
    {
        return $this->twilioClient;
    }

   
    public function getConfig(): TwilioConfig
    {
        return $this->config;
    }
}

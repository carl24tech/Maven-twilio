<?php

namespace TwilioSMS;

use TwilioSMS\Model\SMSMessage;
use TwilioSMS\Exception\TwilioException;

class Client
{
    private string $sid;
    private string $token;
    
    public function __construct(string $sid, string $token)
    {
        $this->validateSid($sid);
        $this->sid = $sid;
        $this->token = $token;
    }
    
    private function validateSid(string $sid): void
    {
        // Check if SID starts with 'AC' and has appropriate length
        if (!preg_match('/^AC[a-f0-9]{32}$/i', $sid)) {
            throw new TwilioException("Invalid Account SID format");
        }
    }
    
    public function sms(): SmsService
    {
        return new SmsService($this);
    }
    
    public function getSid(): string
    {
        return $this->sid;
    }
    
    public function getToken(): string
    {
        return $this->token;
    }
}

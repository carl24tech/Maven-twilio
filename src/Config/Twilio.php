<?php

namespace TwilioSMS\Config;

use TwilioSMS\Exception\TwilioException;

class TwilioConfig
{
    private string $accountSid;
    private string $authToken;
    private ?string $defaultFromNumber;
    private int $timeout;
    private int $retryAttempts;

  
    public function __construct(string $accountSid, string $authToken, array $options = [])
    {
        $this->validateCredentials($accountSid, $authToken);
        
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->defaultFromNumber = $options['default_from_number'] ?? null;
        $this->timeout = $options['timeout'] ?? 30;
        $this->retryAttempts = $options['retry_attempts'] ?? 3;
    }

    private function validateCredentials(string $accountSid, string $authToken): void
    {
        if (empty($accountSid)) {
            throw new TwilioException("Account SID cannot be empty");
        }
        
        if (empty($authToken)) {
            throw new TwilioException("Auth Token cannot be empty");
        }
        
        if (!preg_match('/^AC[a-f0-9]{32}$/', $accountSid)) {
            throw new TwilioException("Invalid Account SID format");
        }
    }

    public function getAccountSid(): string
    {
        return $this->accountSid;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getDefaultFromNumber(): ?string
    {
        return $this->defaultFromNumber;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getRetryAttempts(): int
    {
        return $this->retryAttempts;
    }

    public function toArray(): array
    {
        return [
            'account_sid' => $this->accountSid,
            'default_from_number' => $this->defaultFromNumber,
            'timeout' => $this->timeout,
            'retry_attempts' => $this->retryAttempts
        ];
    }
}

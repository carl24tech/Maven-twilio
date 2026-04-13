<?php

namespace TwilioSMS\Config;

use TwilioSMS\Exception\TwilioException;

class TwilioConfig
{
    private const ACCOUNT_SID_PATTERN = '/^AC[a-f0-9]{32}$/';
    private const E164_PATTERN = '/^\+[1-9]\d{1,14}$/';
    private const MIN_TIMEOUT = 1;
    private const MAX_TIMEOUT = 300;
    private const MAX_RETRY_ATTEMPTS = 10;
    
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
        $this->defaultFromNumber = $this->validateDefaultFromNumber($options['default_from_number'] ?? null);
        $this->timeout = $this->validateTimeout($options['timeout'] ?? 30);
        $this->retryAttempts = $this->validateRetryAttempts($options['retry_attempts'] ?? 3);
    }

    private function validateCredentials(string $accountSid, string $authToken): void
    {
        if (empty($accountSid)) {
            throw new TwilioException("Account SID cannot be empty");
        }
        
        if (empty($authToken)) {
            throw new TwilioException("Auth Token cannot be empty");
        }
        
        if (!preg_match(self::ACCOUNT_SID_PATTERN, $accountSid)) {
            throw new TwilioException(
                "Invalid Account SID format. Expected format: AC followed by 32 hex characters, got: {$accountSid}"
            );
        }
    }
    
    private function validateDefaultFromNumber(?string $number): ?string
    {
        if ($number === null) {
            return null;
        }
        
        if (!preg_match(self::E164_PATTERN, $number)) {
            throw new TwilioException(
                "Default from number must be in E.164 format (e.g., +1234567890), got: {$number}"
            );
        }
        
        return $number;
    }
    
    private function validateTimeout(int $timeout): int
    {
        if ($timeout < self::MIN_TIMEOUT || $timeout > self::MAX_TIMEOUT) {
            throw new TwilioException(
                sprintf("Timeout must be between %d and %d seconds, got: %d", 
                    self::MIN_TIMEOUT, self::MAX_TIMEOUT, $timeout)
            );
        }
        
        return $timeout;
    }
    
    private function validateRetryAttempts(int $attempts): int
    {
        if ($attempts < 0 || $attempts > self::MAX_RETRY_ATTEMPTS) {
            throw new TwilioException(
                sprintf("Retry attempts must be between 0 and %d, got: %d", 
                    self::MAX_RETRY_ATTEMPTS, $attempts)
            );
        }
        
        return $attempts;
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
    
    public function hasDefaultFromNumber(): bool
    {
        return $this->defaultFromNumber !== null;
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
    
    public function toDebugArray(): array
    {
        $maskedToken = strlen($this->authToken) > 8 
            ? substr($this->authToken, 0, 4) . '...' . substr($this->authToken, -4)
            : '***';
            
        return [
            'account_sid' => $this->accountSid,
            'auth_token' => $maskedToken,
            'default_from_number' => $this->defaultFromNumber,
            'timeout' => $this->timeout,
            'retry_attempts' => $this->retryAttempts
        ];
    }
    
    public function with(array $overrides): self
    {
        return new self(
            $overrides['account_sid'] ?? $this->accountSid,
            $overrides['auth_token'] ?? $this->authToken,
            [
                'default_from_number' => $overrides['default_from_number'] ?? $this->defaultFromNumber,
                'timeout' => $overrides['timeout'] ?? $this->timeout,
                'retry_attempts' => $overrides['retry_attempts'] ?? $this->retryAttempts
            ]
        );
    }
}

<?php

namespace TwilioSMS\Model;

class SMSMessage
{
    private string $to;
    private string $body;
    private ?string $from;

    public function __construct(string $to, string $body, ?string $from = null)
    {
        $this->validateTo($to);
        $this->validateBody($body);
        
        $this->to = $to;
        $this->body = $body;
        $this->from = $from;
    }

    private function validateTo(string $to): void
    {
        if (empty($to)) {
            throw new \InvalidArgumentException("Recipient phone number cannot be empty");
        }
        
        // Basic E.164 format validation
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $to)) {
            throw new \InvalidArgumentException("Phone number must be in E.164 format (e.g., +1234567890)");
        }
    }

    private function validateBody(string $body): void
    {
        if (empty($body)) {
            throw new \InvalidArgumentException("Message body cannot be empty");
        }
        
        if (strlen($body) > 1600) {
            throw new \InvalidArgumentException("Message body exceeds 1600 characters");
        }
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): self
    {
        $this->from = $from;
        return $this;
    }
}

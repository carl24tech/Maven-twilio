<?php

namespace TwilioSMS\Model;

use Twilio\Rest\Api\V2010\Account\MessageInstance;
use DateTime;

class SMSResponse
{
    private string $sid;
    private string $status;
    private string $to;
    private string $from;
    private string $body;
    private ?DateTime $dateCreated;
    private ?DateTime $dateSent;
    private ?int $price;
    private ?string $priceUnit;
    private ?string $errorCode;
    private ?string $errorMessage;

    public static function fromTwilioMessage(MessageInstance $message): self
    {
        $response = new self();
        $response->sid = $message->sid;
        $response->status = $message->status;
        $response->to = $message->to;
        $response->from = $message->from;
        $response->body = $message->body;
        $response->dateCreated = $message->dateCreated;
        $response->dateSent = $message->dateSent;
        $response->price = $message->price ? (int)($message->price * 100) : null;
        $response->priceUnit = $message->priceUnit;
        $response->errorCode = $message->errorCode;
        $response->errorMessage = $message->errorMessage;
        
        return $response;
    }

    // Getters
    public function getSid(): string { return $this->sid; }
    public function getStatus(): string { return $this->status; }
    public function getTo(): string { return $this->to; }
    public function getFrom(): string { return $this->from; }
    public function getBody(): string { return $this->body; }
    public function getDateCreated(): ?DateTime { return $this->dateCreated; }
    public function getDateSent(): ?DateTime { return $this->dateSent; }
    public function getPrice(): ?int { return $this->price; }
    public function getPriceUnit(): ?string { return $this->priceUnit; }
    public function getErrorCode(): ?string { return $this->errorCode; }
    public function getErrorMessage(): ?string { return $this->errorMessage; }
    
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }
    
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'undelivered']);
    }
    
    public function toArray(): array
    {
        return [
            'sid' => $this->sid,
            'status' => $this->status,
            'to' => $this->to,
            'from' => $this->from,
            'body' => $this->body,
            'date_created' => $this->dateCreated?->format('Y-m-d H:i:s'),
            'date_sent' => $this->dateSent?->format('Y-m-d H:i:s'),
            'price' => $this->price,
            'price_unit' => $this->priceUnit,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage
        ];
    }
}

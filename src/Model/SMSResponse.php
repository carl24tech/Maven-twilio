<?php

namespace TwilioSMS\Model;

use Twilio\Rest\Api\V2010\Account\MessageInstance;
use DateTime;
use InvalidArgumentException;

class SMSResponse
{
    private string $sid;
    private string $status;
    private string $to;
    private string $from;
    private string $body;
    private ?DateTime $dateCreated;
    private ?DateTime $dateSent;
    private ?float $price;  // Changed from ?int to ?float
    private ?string $priceUnit;
    private ?string $errorCode;
    private ?string $errorMessage;

    private function __construct() {}

    public static function fromTwilioMessage(MessageInstance $message): self
    {
        if (empty($message->sid)) {
            throw new InvalidArgumentException("Invalid message instance: missing SID");
        }
        
        $response = new self();
        $response->sid = $message->sid;
        $response->status = $message->status ?? 'unknown';
        $response->to = $message->to ?? '';
        $response->from = $message->from ?? '';
        $response->body = $message->body ?? '';
        $response->dateCreated = self::parseDate($message->dateCreated);
        $response->dateSent = self::parseDate($message->dateSent);
        $response->price = $message->price !== null ? (float)$message->price : null;
        $response->priceUnit = $message->priceUnit ?? null;
        $response->errorCode = $message->errorCode ?? null;
        $response->errorMessage = $message->errorMessage ?? null;
        
        return $response;
    }
    
    private static function parseDate($date): ?DateTime
    {
        if ($date instanceof DateTime) {
            return $date;
        }
        if (is_string($date) && !empty($date)) {
            return new DateTime($date);
        }
        return null;
    }

    // Getters (unchanged)
    public function getSid(): string { return $this->sid; }
    public function getStatus(): string { return $this->status; }
    public function getTo(): string { return $this->to; }
    public function getFrom(): string { return $this->from; }
    public function getBody(): string { return $this->body; }
    public function getDateCreated(): ?DateTime { return $this->dateCreated; }
    public function getDateSent(): ?DateTime { return $this->dateSent; }
    public function getPrice(): ?float { return $this->price; }  // Changed return type
    public function getPriceUnit(): ?string { return $this->priceUnit; }
    public function getErrorCode(): ?string { return $this->errorCode; }
    public function getErrorMessage(): ?string { return $this->errorMessage; }
    
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }
    
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'undelivered'], true);
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

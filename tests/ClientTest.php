<?php

namespace TwilioSMS\Tests;

use PHPUnit\Framework\TestCase;
use TwilioSMS\Client;
use TwilioSMS\Exception\TwilioException;

class ClientTest extends TestCase
{
    private string $validSid;
    private string $validToken;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->validSid = 'AC' . str_repeat('0', 32);
        $this->validToken = 'test_token';
    }
    
    public function testClientInitialization(): void
    {
        $client = new Client($this->validSid, $this->validToken);
        $this->assertInstanceOf(Client::class, $client);
    }
    
    public function testClientThrowsExceptionWithInvalidSid(): void
    {
        $this->expectException(TwilioException::class);
        new Client('invalid_sid', $this->validToken);
    }
    
    public function testSmsServiceIsAvailable(): void
    {
        $client = new Client($this->validSid, $this->validToken);
        $this->assertNotNull($client->sms());
    }
}

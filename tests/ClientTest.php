<?php

namespace TwilioSMS\Tests;

use PHPUnit\Framework\TestCase;
use TwilioSMS\Client;
use TwilioSMS\Exception\TwilioException;

class ClientTest extends TestCase
{
    public function testClientInitialization(): void
    {
        $client = new Client('AC' . str_repeat('0', 32), 'test_token');
        $this->assertInstanceOf(Client::class, $client);
    }
    
    public function testClientThrowsExceptionWithInvalidSid(): void
    {
        $this->expectException(TwilioException::class);
        new Client('invalid_sid', 'test_token');
    }
    
    public function testSmsServiceIsAvailable(): void
    {
        $client = new Client('AC' . str_repeat('0', 32), 'test_token');
        $this->assertNotNull($client->sms());
    }
}

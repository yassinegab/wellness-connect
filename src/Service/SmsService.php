<?php

namespace App\Service;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsService
{
    private Client $client;
    private string $brand;

    public function __construct(string $apiKey, string $apiSecret, string $brand)
    {
        $credentials = new Basic($apiKey, $apiSecret);
        $this->client = new Client($credentials);
        $this->brand = $brand;
    }

    public function send(string $to, string $message): void
    {
        $sms = new SMS($to, $this->brand, $message);
        $this->client->sms()->send($sms);
    }
}

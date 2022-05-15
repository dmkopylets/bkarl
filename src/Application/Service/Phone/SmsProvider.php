<?php
declare(strict_types=1);

namespace App\Application\Service\Phone;

use App\Application\Service\Log\LoggerService;
use Twilio\Rest\Client;

class SmsProvider
{
    public function __construct(public Client $twilioClient, public string $messagingServiceSid, public LoggerService $loggerService) {}

    public function send(string $destinationPhoneNumber, string $body)
    {
        $message = $this->twilioClient->messages
            ->create($destinationPhoneNumber, [
                "body" => $body,
                "messagingServiceSid" => $this->messagingServiceSid,
            ]);

       $this->loggerService->info('%s {response: %d}', [__METHOD__, $message->sid]);
    }
}
<?php

declare(strict_types=1);

namespace App\Application\Service\Phone;

use App\Application\Service\Log\LoggerService;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;
use Ramsey\Uuid\Uuid;

class PhoneValidator
{
    public function __construct(public Client $twilioClient, public LoggerService $loggerService) { }

    public function validate(string $phoneNumber): bool
    {
        if(getenv('IS_DEV_ENV')){
            return true;
        }

        try {
            $phoneNumber = $this->twilioClient->lookups->v1
                ->phoneNumbers($phoneNumber)
                ->fetch();

        } catch (RestException $exception) {

            if ($exception->getStatusCode() === Response::HTTP_NOT_FOUND) {
                throw new \Exception('Mobile number does not exist or is not valid');
            }

            $exceptionId = Uuid::uuid4();
            $this->loggerService->error(
                '%s {exceptionId: %s; message: %s; trace %s}',
                [__METHOD__, $exceptionId, $exception->getMessage(), $exception->getTraceAsString()]
            );

            throw new \LogicException(
                \sprintf('Something was wrong, please contact support. Your request number is %s.', $exceptionId)
            );
        }

        $this->loggerService->info('%s {response: %d}', [__METHOD__, $phoneNumber->phoneNumber]);

        return true;
    }
}
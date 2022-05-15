<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Features\User\UseCase\RegisterLogin\{Command as RegisterLoginCommand, Handler as RegisterLoginHandler};
use App\Features\User\UseCase\Phone\PinGenerate\{Command as PinGenerateCommand, Handler as PinGenerateHandler};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Features\User\Service\UserService;
use OpenApi\Annotations as OA;
use App\Application\Http\ApiResponse;


/**
 * @OA\Tag(name="FacilityUserFixtures")
 */
class UserController extends AbstractController
{

    public function __construct(public UserService $service){}

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function signUpIn(RegisterLoginCommand $commandReg, RegisterLoginHandler $handlerReg,
        PinGenerateCommand $commandPin, PinGenerateHandler $handlerPin ): Response
    {
        $user = $handlerReg->handle($commandReg);
        $handlerPin->handle($commandPin);

        return ApiResponse::successful('', $user);
    }
}

<?php

namespace App\Controller\Api\User;

use App\Application\Service\Tokens\TokenService;
use App\Features\User\Service\UserService;
use App\Features\User\UseCase\Phone\ApprovePhone\{ Command as PhoneApproveCommand, Handler as PhoneApproveHandler};
use App\Features\User\UseCase\RegisterLogin\{Command as RegisterLoginCommand, Handler as RegisterLoginHandler};
use App\Features\User\UseCase\Logout\{Command as LogoutCommand, Handler as LogoutHandler };
use App\Features\User\UseCase\Phone\PinGenerate\Command as PinGenerateCommand;
use App\Features\User\UseCase\Phone\PinGenerate\Handler as PinGenerateHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Http\ApiResponse;
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(name="Auth")
 **/
#[Route('/auth', 'auth', 'auth.')]
class AuthController extends AbstractController
{
    /**
     * @OA\Post(path="/api/auth/setup",summary="Step 1 - Start page signUp/signIn", description="Step 1 - Start page signUp/signIn",operationId="auth-setup", tags={"Auth"}, security={},
     *     @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *              @OA\Schema(@OA\Property(property="phone",type="string"),required={"phone"}))),
     *              @OA\Response(response=500, description="Not valid data"))
     */
    #[Route('/setup', name: 'setup', methods: ['POST'])]
    public function signUpIn(RegisterLoginCommand $commandReg, RegisterLoginHandler $handlerReg,
        PinGenerateCommand $commandPin, PinGenerateHandler $handlerPin ): Response
    {
        $user = $handlerReg->handle($commandReg);

            $handlerPin->handle($commandPin);

        return ApiResponse::successful('Successfully setup',[
            'status' => $user->getStatus(),
            'phone' => $user->getPhone()
        ]);
    }

    /**
     * @OA\Post(path="/api/auth/phone/approve",summary="Step 2 - Approve user phone, on dev environment 1111 pin is valid",operationId="approve", tags={"Auth"}, security={},
     *    @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="pin",type="string"),
     *                  @OA\Property(property="phone",type="string"),
     *                  required={"pin", "phone"}))),
     *              @OA\Response(response=200, description="
    Object { password, info: {…}, tokens: {…} }
    password:'string'
    info	Object { id,phone,status, … }
    id:'int'
    phone:'string'
    status:'int'
    firstName'string'
    lastName'string'
    tokens	Object { token, refresh_token }
    token:'string'
    refresh_token:'string'
    "),
     *              @OA\Response(response=500, description="Not Valid Code"))
     */
    #[Route('/phone/approve', name: 'approve-phone', methods: 'POST')]
    public function approve(PhoneApproveCommand $command, PhoneApproveHandler $handler, TokenService $tokenService, UserService $userService): ApiResponse
    {
        $user = $userService->getByPhone($command->phone);
        $formatPassword = $handler->handle($command);


        return ApiResponse::successful('Phone approved', [
            'password' => $formatPassword,
            'info' => array_merge($user->jsonSerialize(),
            ),
            'tokens' => $tokenService->getToken($user)
        ]);
    }


    /**
     * @OA\Post(path="/api/auth/phone/resend",summary="Resend pin code",operationId="resend", tags={"Auth"}, security={},
     *     @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="phone",type="string"),
     *                  required={"phone"}
     *              ))),
     *              @OA\Response(response=200, description="{ }"),
     *              @OA\Response(response=500, description="wrong phone number"))
     */
    #[Route('/phone/resend', name: 'resend-phone', methods: ['POST'])]
    public function resend(PinGenerateCommand $command, PinGenerateHandler $handler): ApiResponse
    {
        $handler->handle($command);
        return ApiResponse::successful('Сode resended');
    }

    /**
     * @OA\Post(path="/api/user/login",summary="Login(token generation)Username = phone",operationId="login", description="Live time token 1200 sec = 20 min", tags={"JWT"},security={},
     *     @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="username",type="string"),
     *                  @OA\Property(property="password", type="string"),
     *                  required={"username", "password"}))),
     *     @OA\Response(response=200, description="Token data"),
     *     @OA\Response(response=401, description="Wrong PIN"))
     *
     *
     * @OA\Post(path="/api/user/token/refresh",summary="Refresh token",description="Live time token 86400 sec = 1day", operationId="refresh-token", tags={"JWT"},
     *     @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="refresh_token",type="string"),
     *                  required={"refresh_token"}))),
     *     @OA\Response(response=200, description="
    Object {}
    token:'string'
    refresh_token:'string'
     *     "),
     *     @OA\Response(response=401, description="Wrong PIN"))
     *     security={}
     * )
     */

    /**
     * @OA\Get(path="/api/auth/logout",summary="Logout",operationId="logout", tags={"JWT"},
     *     @OA\Response(response=200, description="{ }"),
     *     @OA\Response(response=403, description="Your token is invalid, please login again to get a new one")
     * )
     */
    #[Route('/logout', name: 'logout')]
    public function logout(LogoutCommand $command, LogoutHandler $handler): Response
    {
        $command->phone = $this->getUser()->getPhone();
        $handler->handle($command);

        return ApiResponse::successful('Logout is successful');
    }

}

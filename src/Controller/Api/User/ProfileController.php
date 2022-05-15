<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Application\Http\ApiResponse;
use App\Application\Service\Tokens\TokenService;
use App\Features\User\Service\UserService;
use App\Features\User\UseCase\CreateProfile\{Command as CreateProfileCommand, Handler as CreateProfileHandler};
use App\Infrastructure\PaginationSerializer\PaginationSerializerInterface;
use App\Features\User\UseCase\Edit\{Command as EditProfileCommand, Handler as EditProfileHandler};
use App\Features\User\UseCase\Phone\PinGenerate\{Command as PinGenerateCommand, Handler as PinGenerateHandler};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Features\User\UseCase\Phone\isUnique\{Command as isUniqueCommand, Handler as isUniqueHandler};
use App\Features\User\UseCase\Phone\EditPhone\Resend\{Command as ResendEditCommand, Handler as ResendEditHandler};
use App\Features\User\UseCase\DeleteData\{Command as DeleteDataCommand, Handler as DeleteDataHandler};

use OpenApi\Annotations as OA;

class ProfileController extends AbstractController
{
    public function __construct(
        public UserService $service,
        public PaginationSerializerInterface $paginationSerializer){}

    /**
     * @OA\Post (path="/api/profile/editProfile",summary="edit profile",operationId="editProfile",tags={"Profile"},
     *  @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="firstName",type="string"),
     *                  @OA\Property(property="lastName",type="string"),
     *                  @OA\Property(property="changePhone",type="string"),
     *                  @OA\Property(property="description",type="string"),
     *                  @OA\Property(property="car",type="string"),
     *       required={"firstName","lastName","changePhone","description","car"}))),
     *          @OA\Response(response=200, description="{ }"))
     */
    #[Route('/profile/editProfile', name: 'editProfile', methods: ['POST'])]
    public function edit(EditProfileCommand $command, EditProfileHandler $handler, PinGenerateCommand $commandPin, PinGenerateHandler $handlerPin): Response
    {
        $command->phone = $this->getUser()->getPhone();
        $handler->handle($command);

        if ($command->phone !== $command->changePhone) {
            $commandPin->phone = $command->phone;

            $handlerPin->handle($commandPin);
        }

        return ApiResponse::successful('Edited');
    }

    /**
     * @OA\Post(path="/api/profile/edit/phone/approve",summary="approve new phone number after editing user profile",operationId="editPhone", tags={"Profile"},
     *    @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="pin",type="string"),
     *                  required={"pin"}))),
     *              @OA\Response(response=200, description="Approved"),
     *              @OA\Response(response=500, description="Not valid PIN"))
     */
    #[Route('/profile/edit/phone/approve', name: 'edit-phone-approve', methods: 'POST')]
    public function approve(isUniqueCommand $command, isUniqueHandler $handler, TokenService $tokenService)
    {
        $user = $this->getUser();
        $command->phone = $this->getUser()->getPhone();
        $command->changePhone = $this->getUser()->getChangedPhone();
        $handler->handle($command);

        return ApiResponse::successful('New phone confirmed', $tokenService->getToken($user));
    }

    /**
     * @OA\Post(path="/api/profile/edit/resend",summary="Resend pin code",operationId="resend", tags={"Profile"},
     *              @OA\Response(response=200, description="{ }"),
     *              @OA\Response(response=500, description="wrong phone number"))
     */
    #[Route('/profile/edit/resend', name: 'resend-phone', methods: ['POST'])]
    public function resend(ResendEditCommand $command, ResendEditHandler $handler): ApiResponse
    {
        $command->phone = $this->getUser()->getChangedPhone();
        $handler->handle($command);
        return ApiResponse::successful('Сode resended');
    }

    /**
     *   @OA\Post(path="/api/profile/delete",summary="Type DELETE to replace your presonal data by Deleted",operationId="delete personal data", tags={"Profile"},
     *    @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="deleteCommand",type="string", description="type DELETE or delete"),
     *                  required={"deleteCommand"}))),
     *              @OA\Response(response=200, description="{ }"),
     *              @OA\Response(response=500, description="Wrong command"))
     */
    #[Route('/profile/delete', name: 'deleteProfile', methods: "POST")]
    public function deletePersonalData(DeleteDataCommand $command, DeleteDataHandler $handler): ApiResponse
    {
        $command->phone = $this->getUser()->getPhone();
        $user = $this->getUser();
        $handler->handle($command);
        return ApiResponse::successful('Personal data cleaned');
    }

    /**
     * @OA\Get(path="/api/profile",summary="Profile info",operationId="get-profile-info", tags={"Profile"},
     *     @OA\Response(response=200, description="
    id:'int'
    phone:'string|null'
    status:'int'
    firstName:'string'
    lastName:'string'
    description:'string|null'
    car:'string|null'")
     * )
     */
    #[Route('/profile/', name: 'getInfo')]
    public function getInfo(): Response
    {
        return ApiResponse::successful('', $this->getUser());
    }

    /**
     * @OA\Post (path="/api/user/createProfile",summary="Step 4 - create prorile",operationId="createProfile",tags={"Profile"},
     *      @OA\RequestBody(
     *           required=true,
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                  @OA\Property(property="firstName",type="string"),
     *                  @OA\Property(property="lastName",type="string"),
     *                  @OA\Property(property="description",type="string"),
     *                  @OA\Property(property="car",type="string"),
     *       required={"firstName","lastName","description","car"}))),
     *          @OA\Response(response=200, description="{ }"))
     */
    #[Route('/user/createProfile', name: 'createProfile', methods: ['POST'])]
    public function createProfile(CreateProfileCommand $command, CreateProfileHandler $handler): Response
    {
        $command->phone = $this->getUser()->getPhone();
        dd($this->getUser()); die;
        $handler->handle($command);

        return ApiResponse::successful('Successful created user profile');
    }
}

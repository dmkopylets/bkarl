<?php

declare(strict_types=1);

namespace App\Application\Http\RequestResolver;

use App\Application\Http\DTO\BaseDataObject;
use App\Application\Http\Validation\ValidationCheckerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RequestObjectResolver implements ArgumentValueResolverInterface
{
    use ValidationCheckerTrait;

    public function __construct(public DenormalizerInterface $serializer) { }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), BaseDataObject::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $request->request->all();

        if (Request::METHOD_GET === $request->getMethod()) {
            $data = $request->query->all();
        }

        if($request->files->count()){
              $data = array_merge($data, $request->files->all());
        }

        $dto = $this->serializer->denormalize($data, $argument->getType(), null, [
            'disable_type_enforcement' => true,
        ]);

        $this->validate($dto);

        yield $dto;
    }
}

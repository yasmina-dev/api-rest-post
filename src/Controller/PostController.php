<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api")
 * Class PostController
 * @package App\Controller
 */
class PostController extends AbstractController
{
    /**
     * @Route("/posts", name=" creation.post", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
       ValidatorInterface $validator,
    EntityManagerInterface $entityManager
)
    {
        $post = $serializer->deserialize($request->getContent(),Post::class,'json',[
            ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true
        ]);

        $errors = $validator->validate($post);
        $responseErrors=[];
      if($errors->count())
      {
          foreach ($errors as $error) {

              /** @var ConstraintViolation $error */

              $nameProperty = $error->getPropertyPath();
              $messageError = $error->getMessage();

              $responseErrors['errors'][$nameProperty][]=$messageError;
          }

          return $this->json($responseErrors, 404);
      }

      $entityManager->persist($post);
      $entityManager->flush();

      return $this->json($post, 201);
    }
}

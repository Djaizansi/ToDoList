<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    protected function getArrayMessage($message, $errors=null){
        $data = [
            'title' => $message,
        ];

        if(!is_null($errors)){
            $data['errors'] = $errors;
        }
        return $data;
    }

    /**
     * @Route("/register", name="api_create_user", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);
        $form->submit($request->request->all());

        if($form->isValid()){
            $alreadyExist = $em->getRepository(User::class)->findByEmail($user->getEmail());
            if(!$user->isValid()){
                return new JsonResponse($this->getArrayMessage("L'utilisateur n'est pas valide", 500),500);
            }

            if(!empty($alreadyExist)){
                return new JsonResponse($this->getArrayMessage("L'utilisateur existe déjà", 500),500);
            }
            $em->persist($form->getData());
            $em->flush();

            return new JsonResponse($this->getArrayMessage('L\'utilisateur a bien été crée'),201);
        }

        return new JsonResponse($this->getArrayMessage("Une erreur lors de la validation",$form->getErrors()), 500);
    }
}

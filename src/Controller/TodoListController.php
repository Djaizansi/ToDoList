<?php

namespace App\Controller;


use App\Entity\TodoList;
use App\Entity\User;
use App\Form\TodoListType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoListController extends AbstractController
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
     * @Route("/todolist/create/{id}", name="api_create_todolist", methods={"POST"})
     */

    public function create($id,Request $request, User $user = null, EntityManagerInterface $em){
        if(!$user){
            return new JsonResponse($this->getArrayMessage("L'utilisateur n'est pas connecté ou n'existe pas", 500),500);
        }
        if($user->getTodoList()){
            return new JsonResponse($this->getArrayMessage("L'utilisateur a déjà une todolist", 500),500);
        }

        $todolist = new TodoList();
        $form = $this->createForm(TodoListType::class,$todolist);
        $form->submit($request->request->all());

        if($form->isValid()){
            $todolist->setUser($user);
            if(!$todolist->isValid()){
                return new JsonResponse($this->getArrayMessage("La Todolist n'est pas valide", 500),500);
            }
            $em->persist($todolist);
            $em->flush();

            return new JsonResponse($this->getArrayMessage('La Todolist a bien été crée'),201);
        }
        return new JsonResponse($this->getArrayMessage("Test"),201);
    }
}
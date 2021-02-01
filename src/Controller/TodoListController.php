<?php

namespace App\Controller;


use App\Entity\Item;
use App\Entity\TodoList;
use App\Form\ItemType;
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
     * @Route("/todolist/create", name="api_create_todolist", methods={"POST"})
     */

    public function create(Request $request, EntityManagerInterface $em){
        if(!$this->getUser()) return new JsonResponse($this->getArrayMessage("L'utilisateur n'est pas connecté ou n'existe pas", 500),500);
        if($this->getUser()->getTodoList()) return new JsonResponse($this->getArrayMessage("L'utilisateur a déjà une todolist", 500),500);

        $todolist = new TodoList();
        $form = $this->createForm(TodoListType::class,$todolist);
        $form->submit($request->request->all());
        if($form->isValid()){
            $alreadyExist = $em->getRepository(TodoList::class)->findByName($todolist->getName());
            $todolist->setUser($this->getUser());
            if(!$todolist->isValid()){
                return new JsonResponse($this->getArrayMessage("La Todolist n'est pas valide", 500),500);
            }
            if(!empty($alreadyExist)){
                return new JsonResponse($this->getArrayMessage("Le nom de la todolist existe déjà", 500),500);
            }
            $em->persist($todolist);
            $em->flush();

            return new JsonResponse($this->getArrayMessage('La Todolist a bien été crée'),201);
        }
        return new JsonResponse($this->getArrayMessage("Formulaire non valide"),500);
    }

    /**
     * @Route("/todolist/add/item", name="api_add_item_todolist", methods={"POST"})
     */
    public function addItemToList(Request $request, EntityManagerInterface $em)
    {
        //Utilisateur connecté ?
        if(!$this->getUser()) return new JsonResponse($this->getArrayMessage("L'utilisateur n'est pas connecté ou n'existe pas", 500),500);

        //Recuperer la todolist de l'utilisateur
        $userTodoList = $this->getUser()->getTodolist();
        //Création de l'item
        $item = new Item();
        $form = $this->createForm(ItemType::class,$item);
        $form->submit($request->request->all());

        if($form->isValid()){
            $item = $form->getData();
            $item->setCreatedAt(new \DateTime('now'));

            //Check if item's name alreayExist
            $alreadyExist = $em->getRepository(Item::class)->findBy(['name' => $item->getName()]);
            if(!empty($alreadyExist)){
                return new JsonResponse($this->getArrayMessage("Le nom de l'item existe déjà", 500),500);
            }
            //Check if item's valid
            if(!$item->isValid()){
                return new JsonResponse($this->getArrayMessage("L'item n'est pas valide", 500),500);
            }
            //Check if at least one item exist
            $itemInDb = count($em->getRepository(Item::class)->findAll());

            if($itemInDb > 0){
                //Vérifier si on peut ajouter l'item à la todolist
                $todolist = $userTodoList;
                try {
                    $todolist->canAddItem($item);
                }catch(\Exception $e) {
                    return new JsonResponse($this->getArrayMessage($e->getMessage(), 500),500);
                }
            }

            $item->setTodolist($userTodoList);
            $em->persist($item);
            $em->flush();

            return new JsonResponse($this->getArrayMessage("L'item a bien été ajouté à la todolist : ".ucfirst($userTodoList->getName())),201);
        }
        return new JsonResponse($this->getArrayMessage("Une erreur lors de la validation",$form->getErrors()), 500);
    }
}
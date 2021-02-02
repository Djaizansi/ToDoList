<?php

namespace App\Tests\Controller;

use App\Entity\Item;
use App\Entity\TodoList;
use App\Entity\User;
use App\Service\HelpersService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DateTime;

class TodoListControllerTest extends WebTestCase
{
    private $user;
    private $client;
    private $item;
    private $em;

    protected function setUp(): void
    {
        parent::setUp();
        $emailRandom = HelpersService::createEmailRandom();
        $birthday = HelpersService::createOldBirthday();
        $nameTodolist = HelpersService::createStringRandom();
        $nameItem = HelpersService::createStringRandom();

        $this->user = [
            'lastname' => 'Jallali',
            'firstname' => 'Youcef',
            'email' => $emailRandom,
            'password' => 'azddzed',
            'birthday' => $birthday
        ];

        $this->item = [
            'name' => $nameItem,
            'content' => 'Faire projet pour partiel',
        ];

        $this->todolist = [
            'name' => $nameTodolist,
            'description' => 'cccccc'
        ];

        $this->client = static::createClient();
        $myKernel = self::$kernel->getContainer();
        $this->em = $myKernel->get('doctrine.orm.default_entity_manager');

    }

    // TODOLIST : GESTION CREATE TODOLIST
    public function testUserNotConnected()
    {
        $this->client->request('POST','/login',$this->user);
        $this->client->request('POST','/todolist/create',$this->user);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'utilisateur n'est pas connecté ou n'existe pas", $content);
    }

    public function testUserGetTodolist()
    {
        //Get user bdd
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->user['password'] = 'azertyuiop';

        $this->client->request('POST','/login',$this->user);
        $this->client->request('POST','/todolist/create',$this->user);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'utilisateur a déjà une todolist", $content);
    }

    public function testSuccessTodolist()
    {
        //Get user bdd
        $this->user['email'] = 'Djaizansi359@hotmail.com';
        $this->user['password'] = 'azfezfez3';
        //Connexion
        $this->client->request('POST','/login',$this->user);
        $this->client->request('POST','/todolist/create',$this->todolist);
        //Reset todolist
        $user = $this->em->getRepository(User::class)->findByEmail(['email'=> $this->user['email']])[0];
        $getTodolist = $this->em->getRepository(TodoList::class)->find($user->getTodolist());
        $this->em->remove($getTodolist);
        $this->em->flush();
        //JsonResponse
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("La Todolist a bien été crée", $content);
    }

    public function testNameExistTodolist()
    {
        //Get user bdd
        $this->user['email'] = 'Djaizansi359@hotmail.com';
        $this->user['password'] = 'azfezfez3';

        $this->todolist['name'] = 'dddddd';
        $this->client->request('POST','/login',$this->user);
        $this->client->request('POST','/todolist/create',$this->todolist);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("Le nom de la todolist existe déjà", $content);
    }

    // TODOLIST : GESTION ADD ITEM TO TODOLIST
    public function testAddItemSuccess()
    {
        //Get user bdd
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->user['password'] = 'azertyuiop';

        $user = $this->em->getRepository(User::class)->findByEmail(['email'=> $this->user['email']])[0]->getTodolist();
        $nameTodolist = ucfirst($user->getName());

        $item = $this->em->getRepository(Item::class)->getLastItem($user->getId())[0];
        $formatDateItem = $item->getCreatedAt()->format('Y-m-d H:i:s');

        //Add Date create item
        $actual = new DateTime($formatDateItem, new \DateTimeZone('Europe/Paris'));
        $myDateCreate = $actual->add(new \DateInterval('PT35M'));
        $this->item['createdAt'] = $myDateCreate->format('Y-m-d H:i:s');

        $this->client->request('POST','/login',$this->user);
        $this->client->request('POST','/todolist/add/item',$this->item);

        $getItem = $this->em->getRepository(Item::class)->findByName(['name' => $this->item['name']])[0];
        $this->em->remove($getItem);
        $this->em->flush();

        $content = json_decode($this->client->getResponse()->getContent())->title;

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'item a bien été ajouté à la todolist : $nameTodolist", $content);
    }

    public function testNameItemExist()
    {
        //Get user bdd
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->user['password'] = 'azertyuiop';

        $user = $this->em->getRepository(User::class)->findByEmail(['email'=> $this->user['email']])[0]->getTodolist();
        $item = $this->em->getRepository(Item::class)->getLastItem($user->getId())[0];
        $formatDateItem = $item->getCreatedAt()->format('Y-m-d H:i:s');

        //Add Date create item
        $actual = new DateTime($formatDateItem, new \DateTimeZone('Europe/Paris'));
        $myDateCreate = $actual->add(new \DateInterval('PT35M'));
        $this->item['createdAt'] = $myDateCreate->format('Y-m-d H:i:s');

        $this->client->request('POST','/login',$this->user);
        //Add First Item
        $this->client->request('POST','/todolist/add/item',$this->item);
        //Add Second Item
        $this->client->request('POST','/todolist/add/item',$this->item);

        //Suppression de l'item généré au début
        $getItem = $this->em->getRepository(Item::class)->findByName(['name' => $this->item['name']])[0];
        $this->em->remove($getItem);
        $this->em->flush();

        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("Le nom de l'item existe déjà", $content);
    }

    public function testItemTooRecent()
    {
        //Get user bdd
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->user['password'] = 'azertyuiop';

        $user = $this->em->getRepository(User::class)->findByEmail(['email'=> $this->user['email']])[0]->getTodolist();
        $item = $this->em->getRepository(Item::class)->getLastItem($user->getId())[0];
        $formatDateItem = $item->getCreatedAt()->format('Y-m-d H:i:s');

        //Add Date create item
        $actual = new DateTime($formatDateItem, new \DateTimeZone('Europe/Paris'));
        $myDateCreate = $actual->add(new \DateInterval('PT35M'));
        $this->item['createdAt'] = $myDateCreate->format('Y-m-d H:i:s');
        $nameFirstItem = $this->item['name'];

        $this->client->request('POST','/login',$this->user);
        //Add First Item
        $this->client->request('POST','/todolist/add/item',$this->item);
        //Add Second Item
        $nameItem = HelpersService::createStringRandom();
        $this->item['name'] = $nameItem;
        $this->client->request('POST','/todolist/add/item',$this->item);

        //Suppression de l'item généré au début
        $getItem = $this->em->getRepository(Item::class)->findByName(['name' => $nameFirstItem])[0];
        $this->em->remove($getItem);
        $this->em->flush();

        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("Le dernier item est récent. Veuillez respecter les 30 minutes d'écart", $content);
    }

    public function testMaxItemInTodolist()
    {
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->user['password'] = 'azertyuiop';
        $arrayNameUniq = [];

        $user = $this->em->getRepository(User::class)->findByEmail(['email'=> $this->user['email']])[0]->getTodolist();
        $sizeTodolist = count($user->getItem()->getValues());
        $item = $this->em->getRepository(Item::class)->getLastItem($user->getId())[0];
        $formatDateItem = $item->getCreatedAt()->format('Y-m-d H:i:s');

        $boucleItem = 9 - $sizeTodolist;

        //Add Date create item
        $actual = new DateTime($formatDateItem, new \DateTimeZone('Europe/Paris'));
        $myDateCreate = $actual->add(new \DateInterval('PT35M'));
        $this->item['createdAt'] = $myDateCreate->format('Y-m-d H:i:s');
        $nameFirstItem = $this->item['name'];

        $this->client->request('POST','/login',$this->user);
        //Add First Item
        $arrayNameUniq[] = $this->item['name'];
        $this->client->request('POST','/todolist/add/item',$this->item);
        for($i = 0; $i<=$boucleItem;$i++){
            $nameItem = HelpersService::createStringRandom();
            $this->item['name'] = $nameItem;
            $item = $this->em->getRepository(Item::class)->getLastItem($user->getId())[0];
            $formatDateItem = $item->getCreatedAt()->format('Y-m-d H:i:s');

            $actual = new DateTime($formatDateItem, new \DateTimeZone('Europe/Paris'));
            $myDateCreate = $actual->add(new \DateInterval('PT35M'));
            $this->item['createdAt'] = $myDateCreate->format('Y-m-d H:i:s');
            $arrayNameUniq[] = $this->item['name'];
            $this->client->request('POST','/todolist/add/item',$this->item);
        }

        for($i=0;$i<count($arrayNameUniq)-1;$i++){
            $getItem = $this->em->getRepository(Item::class)->findByName(['name' => $arrayNameUniq[$i]])[0];
            $this->em->remove($getItem);
            $this->em->flush();
        }

        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("La todo list possède beaucoup d'item", $content);
    }
}
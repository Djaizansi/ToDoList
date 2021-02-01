<?php

namespace App\Tests\Controller;

use App\Entity\TodoList;
use App\Entity\User;
use App\Service\HelpersService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        $myToday = new \DateTime('now');
        $createdItemAt = $myToday->add(new \DateInterval('PT45M'));

        $this->user = [
            'lastname' => 'Jallali',
            'firstname' => 'Youcef',
            'email' => $emailRandom,
            'password' => 'azddzed',
            'birthday' => $birthday
        ];

        $this->item = [
            'name' => 'TestInteg',
            'content' => 'Faire projet pour partiel',
            'createdAt' => $createdItemAt
        ];

        $this->todolist = [
            'name' => $nameTodolist,
            'description' => 'cccccc'
        ];

        $this->client = static::createClient();
        $myKernel = self::$kernel->getContainer();
        $this->em = $myKernel->get('doctrine.orm.default_entity_manager');

    }

    //User not exist
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
}
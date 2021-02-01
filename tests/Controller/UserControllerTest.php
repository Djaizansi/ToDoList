<?php

namespace App\Tests\Controller;

use App\Service\HelpersService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $user;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $emailRandom = HelpersService::createEmailRandom();
        $birthday = HelpersService::createOldBirthday();

        $this->user = [
            'lastname' => 'Jallali',
            'firstname' => 'Youcef',
            'email' => $emailRandom,
            'password' => 'azfezfez3',
            'birthday' => $birthday
        ];
        $this->client = static::createClient();
    }

    public function testAddUser()
    {
        $this->client->request('POST', '/register',$this->user);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'utilisateur a bien été crée", $content);
    }

    public function testEmailExist()
    {
        $this->user['email'] = 'youcef.jallali@gmail.com';
        $this->client->request('POST', '/register',$this->user);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'utilisateur existe déjà", $content);
    }

    public function testYoungUser()
    {
        $birthday = new \DateTime($this->user['birthday']);
        $this->user['birthday'] = $birthday->add(new \DateInterval('P30Y'))->format('Y-m-d');
        $this->client->request('POST', '/register',$this->user);
        $content = json_decode($this->client->getResponse()->getContent())->title;
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("L'utilisateur n'est pas valide", $content);
    }

    public function testConnexionSuccess()
    {
        $myUser = ['email' => 'youcef.jallali@gmail.com','password'=>'azertyuiop'];
        $this->client->request('POST', '/login', $myUser);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
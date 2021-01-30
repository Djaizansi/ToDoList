<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $user;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $today = new \DateTime('now');
        $birthday = $today->sub(new \DateInterval('P30Y'))->format('Y-m-d');

        $arrayOfFirstname = ['Youcef','Marwane','Khalil','Clara','Djaizansi'];
        $arrayOfDomain = ['hotmail','yahoo','gmail','youcef','yopmail'];

        //Melange les elements des tableaux
        shuffle($arrayOfDomain);
        shuffle($arrayOfFirstname);

        //Formation de l'email random car unique
        $keyF = array_rand($arrayOfFirstname,1);
        $keyD = array_rand($arrayOfDomain,1);
        $randomInt = random_int(1, 1000);

        $emailRandom = $arrayOfFirstname[$keyF].$randomInt.'@'.$arrayOfDomain[$keyD].'.com';

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
}
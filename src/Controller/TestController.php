<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {
        $today = new DateTime('now');
        $birthday = $today->sub(new \DateInterval('P30Y'))->format('Y-m-d');
        $user = new User(
            'Jallali',
            'Youcef',
            'youcef.jallali@gmail.com',
            'azerty123',
            "$birthday"
        );
        dd($user);
        return $this->render('test/index.html.twig');
    }
}

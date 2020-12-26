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
        // QUELQUES TEST ICI

        $today = new DateTime('now');
        $birthday = $today->sub(new \DateInterval('P30Y'))->format('Y-m-d');
        $user = new User(
            'Jallali',
            'Youcef',
            'youcef.jallali@gmail.com',
            'az3',
            "$birthday"
        );

        $newToday = new DateTime();
        $dateChoiceToday = new DateTime('2020-12-25 19:36:00');

        $diffDate = $newToday->diff($dateChoiceToday);
        $difference = $diffDate->format('%H:%I');
        if($difference > '02:30'){
            echo "bjr";
        }
        return $this->render('test/index.html.twig');
    }
}

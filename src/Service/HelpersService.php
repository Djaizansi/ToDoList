<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class HelpersService {

    private $em;
    private $userRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo){
        $this->em = $em;
        $this->userRepo = $userRepo;
    }

    public static function createEmailRandom(){
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

        return $emailRandom;
    }

    public static function createOldBirthday(){
        $today = new \DateTime('now');
        $birthday = $today->sub(new \DateInterval('P30Y'))->format('Y-m-d');
        return $birthday;
    }

    public static function createStringRandom(){
        $listOfLetter = 'abcdefghijklmnopqrstuvwxyz'; //Permet de prendre lettres
        $listOfLetter = str_shuffle($listOfLetter);
        $str = substr($listOfLetter, 0, 7);
        return $str;
    }
}
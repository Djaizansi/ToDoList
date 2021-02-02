<?php

namespace App\Service;

use Swift_Mailer;

class EmailService
{
    private $mailer;

    public function __construct(Swift_Mailer $mailer){
        $this->mailer = $mailer;
    }

    public function sendMail(string $title, string $message, string $email)
    {
        $myEmail = (new \Swift_Message($title))
            ->setFrom('assobookpa@gmail.com')
            ->setTo($email)
            ->setBody($message);
        //Send Swift_Message Object
        $this->mailer->send($myEmail);
    }
}
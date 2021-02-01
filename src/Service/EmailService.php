<?php

namespace App\Service;

use Swift_Mailer;

class EmailService
{
    public function sendMail(string $message, string $email, Swift_Mailer $mailer)
    {
        $myEmail = (new Swift_Message('Alert TodoList'))
            ->setFrom('send@example.com')
            ->setTo($email)
            ->setBody($message);

        $mailer->send($myEmail);
    }
}
<?php

namespace App\Test;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use DateTime;

class UserTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $today = new DateTime('now');
        $birthday = $today->sub(new \DateInterval('P30Y'))->format('Y-m-d');

        $this->user = new User(
            'Jallali',
            'Youcef',
            'youcef.jallali@gmail.com',
            'azfezfez3',
            "$birthday"
        );
    }

    public function testIsValidNominal()
    {
        $this->assertTrue($this->user->isValid());
    }

    public function testIsNotPasswordSizeValid()
    {
        $this->user->setPassword('123');
        $this->assertFalse($this->user->isValid());
    }

    public function testIsNotPasswordEmptyValid()
    {
        $this->user->setPassword('');
        $this->assertFalse($this->user->isValid());
    }

    public function testIsNotEmailFormatValid()
    {
        $this->user->setEmail('azerty');
        $this->assertFalse($this->user->isValid());
    }

    public function testIsNotFirstnameEmptyValid()
    {
        $this->user->setFirstname('');
        $this->assertFalse($this->user->isValid());
    }

    public function testIsNotLastnameValid()
    {
        $this->user->setLastname('');
        $this->assertFalse($this->user->isValid());
    }
}

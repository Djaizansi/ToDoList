<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeZone;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @ORM\OneToOne(targetEntity=TodoList::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $todoList;

    public function __construct(string $firstname, string $lastname, string $email, string $password, string $birthday)
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->birthday = $birthday;
    }

    public function isValid(): bool
    {
        $date = new DateTime($this->birthday,new DateTimeZone('Europe/Paris'));
        $today = new DateTime('now',new DateTimeZone('Europe/Paris'));
        $age = $date->diff($today)->y;

        return !empty($this->firstname)
            && !empty($this->lastname)
            && !empty($this->email)
            && !empty($this->password)
            && !empty($this->birthday)
            && strlen($this->password) >= 8
            && strlen($this->password) <= 40
            && filter_var($this->email, FILTER_VALIDATE_EMAIL)
            && $age >= 13;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(TodoList $todoList): self
    {
        // set the owning side of the relation if necessary
        if ($todoList->getUser() !== $this) {
            $todoList->setUser($this);
        }

        $this->todoList = $todoList;

        return $this;
    }
}

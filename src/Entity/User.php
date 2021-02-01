<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("api")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("api")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("api")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("api")
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("api")
     */
    private $birthday;

    /**
     * @ORM\Column(type="json")
     * @Groups("api")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity=TodoList::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups("api")
     */
    private $todoList;

    public function __construct(string $firstname=null, string $lastname=null, string $email=null, string $password=null, string $birthday=null)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->birthday = new DateTime($birthday);
    }

    public function isValid(): bool
    {
        if(!is_null($this->birthday)){
            $date = $this->birthday;
            $today = new DateTime('now');
            $age = $date->diff($today)->y;
        }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(?TodoList $todoList): self
    {
        // unset the owning side of the relation if necessary
        if ($todoList === null && $this->todoList !== null) {
            $this->todoList->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($todoList !== null && $todoList->getUser() !== $this) {
            $todoList->setUser($this);
        }

        $this->todoList = $todoList;

        return $this;
    }
}

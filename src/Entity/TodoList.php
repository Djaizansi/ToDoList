<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use App\Service\EmailService;
use Exception;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TodoListRepository::class)
 */
class TodoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="todoList", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="todoList", orphanRemoval=true)
     */
    private $item;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    public function isValid(): bool
    {
        return !empty($this->name)
            && !empty($this->user)
            && strlen($this->name) <= 255
            && is_null($this->description) || strlen($this->description) <= 255;
    }

    public function canAddItem(Item $item)
    {
        $today = new DateTime();
        $lastItem = $this->getLastItem();

        $diffDate = $today->diff($lastItem->getCreatedAt());
        $outputMinute = $diffDate->format('%H:%I');

        if(is_null($item) || !$item->isValid()){
            throw new Exception('L\'item est nul ou invalide');
        }

        if(is_null($this->user) || !$this->user->isValid())
        {
            throw new Exception('L\'utilisateur est nul ou invalide');
        }

        if($this->getSizeTodoList() >= 10)
        {
            throw new Exception('La todo list possède beaucoup d\'item');
        }

        if(!is_null($lastItem) && $outputMinute < '00:30')
        {
            throw new Exception('Le dernier item est récent. Veuillez respecter les 30 minutes d\'écart');
        }

        $this->numberItemAlert();

        return $item;
    }

    public function numberItemAlert()
    {
        if($this->getSizeTodoList() == 8)
        {
            $this->sendEmailUser();
            return true;
        }
    }

    protected function sendEmailUser()
    {
        $emailService = new EmailService();
        $mailer = new \Swift_Mailer();
        $emailService->sendMail('Il vous reste 2 items',$this->user->getEmail(), $mailer);
    }

    protected function getLastItem()
    {
        return $this->getItem()->last();
    }

    protected function getSizeTodoList()
    {
        return sizeof($this->getItem());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItem(): Collection
    {
        return $this->item;
    }

    public function removeItem(Item $item): self
    {
        if ($this->item->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getTodoList() === $this) {
                $item->setTodoList(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}

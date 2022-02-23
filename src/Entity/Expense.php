<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['expense:read']],
        'denormalization_context' => ['groups' => ['expense:write']],
    ],
)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["expense:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["expense:read", "expense:write"])]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(["expense:read", "expense:write"])]
    private $description;

    #[ORM\Column(type: 'float')]
    #[Groups(["expense:read", "expense:write"])]
    private $price;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["expense:read"])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    private $made_by;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    private $expenseGroup;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participatedExpenses')]
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMadeBy(): ?User
    {
        return $this->made_by;
    }

    public function setMadeBy(?User $made_by): self
    {
        $this->made_by = $made_by;

        return $this;
    }

    public function getExpenseGroup(): ?Group
    {
        return $this->expenseGroup;
    }

    public function setExpenseGroup(?Group $expenseGroup): self
    {
        $this->expenseGroup = $expenseGroup;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}

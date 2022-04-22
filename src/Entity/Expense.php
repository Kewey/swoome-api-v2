<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ApiResource(
    attributes: [
        "force_eager" => false,
        'normalization_context' => ['groups' => ['expense:read'], "enable_max_depth" => true],
        'denormalization_context' => ['groups' => ['expense:write'], "enable_max_depth" => true],
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

    #[ORM\Column(type: 'integer')]
    #[Groups(["expense:read", "expense:write"])]
    private $price;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["expense:read"])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["expense:read", "expense:write", "user:read", "group:read"])]
    private $madeBy;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'expenses')]
    #[ApiSubresource]
    #[Groups(["expense:read", "expense:write", "group:read"])]
    #[ORM\JoinColumn(nullable: false)]
    private $expenseGroup;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participatedExpenses')]
    #[Groups(["expense:read", "expense:write", "user:read"])]
    private $participants;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(["expense:read", "expense:write"])]
    private $expenseAt;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
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
        return $this->madeBy;
    }

    public function setMadeBy(?User $madeBy): self
    {
        $this->madeBy = $madeBy;

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

    public function getExpenseAt(): ?\DateTimeImmutable
    {
        return $this->expenseAt;
    }

    public function setExpenseAt(\DateTimeImmutable $expenseAt): self
    {
        $this->expenseAt = $expenseAt;

        return $this;
    }
}

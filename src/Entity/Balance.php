<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BalanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BalanceRepository::class)]
#[ApiResource(
    attributes: [
        "force_eager" => false,
        'normalization_context' => ['groups' => ['balance:read'], "enable_max_depth" => true],
        'denormalization_context' => ['groups' => ['balance:write'], "enable_max_depth" => true],
    ],
)]
class Balance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Expense::class, inversedBy: 'balances')]
    private $expense;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'balances')]
    #[Groups(["expense:read", 'balance:read'])]
    private $balanceUser;

    #[ORM\Column(type: 'integer')]
    #[Groups(["expense:read", 'balance:read'])]
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpense(): ?Expense
    {
        return $this->expense;
    }

    public function setExpense(?Expense $expense): self
    {
        $this->expense = $expense;

        return $this;
    }

    public function getBalanceUser(): ?User
    {
        return $this->balanceUser;
    }

    public function setBalanceUser(?User $balanceUser): self
    {
        $this->balanceUser = $balanceUser;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}

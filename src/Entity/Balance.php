<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BalanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BalanceRepository::class)]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['balance:read']],
        'denormalization_context' => ['groups' => ['balance:write']],
    ],
    collectionOperations: [
        'post',
        'get' => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Désolé, vous devez être admin pour voir toutes les balances.",
        ],
    ],
)]
class Balance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'balances')]
    #[Groups(["group:read", 'balance:read'])]
    private $balanceUser;

    private $refundTemporaryValue;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'balances')]
    #[ORM\JoinColumn(nullable: false)]
    private $balanceGroup;

    public function getId(): ?int
    {
        return $this->id;
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

    #[Groups("group:read", 'balance:read')] // <- MAGIC IS HERE, you can set a group on a method.
    public function getValue(): ?int
    {
        $balanceValue = 0;

        foreach ($this->getExpenses() as $expense) {
            if ($this->balanceUser == $expense->getMadeBy()) {
                if ($expense->getParticipants()->count() == 1 && !$expense->getParticipants()->contains($this->balanceUser)) {
                    $balanceValue += $expense->getPrice();
                } else {
                    $balanceValue += $expense->getPrice() - ($expense->getPrice() / $expense->getParticipants()->count());
                }
            } elseif ($expense->getParticipants()->contains($this->balanceUser)) {
                $balanceValue += - ($expense->getPrice() / $expense->getParticipants()->count());
            } else {
                $balanceValue += 0;
            }
        }

        if (-3 < $balanceValue && $balanceValue < 3) {
            $balanceValue = 0;
        }

        $this->refundTemporaryValue = $balanceValue;

        return $balanceValue;
    }

    public function getRefundTemporaryValue(): ?int
    {
        return $this->refundTemporaryValue;
    }

    public function setRefundTemporaryValue(int $refundTemporaryValue): self
    {
        $this->refundTemporaryValue = $refundTemporaryValue;

        return $this;
    }

    public function getBalanceGroup(): ?Group
    {
        return $this->balanceGroup;
    }

    public function setBalanceGroup(?Group $balanceGroup): self
    {
        $this->balanceGroup = $balanceGroup;

        return $this;
    }

    public function getExpenses(): array
    {
        $expenses = [];
        foreach ($this->balanceGroup->getExpenses() as $expense) {
            if ($expense->getParticipants()->contains($this->balanceUser)) {
                $expenses[] = $expense;
            } elseif ($expense->getMadeBy() == $this->balanceUser) {
                $expenses[] = $expense;
            }
        }
        return $expenses;
    }
}

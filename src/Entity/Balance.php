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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'balances')]
    #[Groups(["group:read", 'balance:read'])]
    private $balanceUser;

    #[ORM\Column(type: 'integer')]
    #[Groups(["group:read", 'balance:read'])]
    private $value;

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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

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
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RefundRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RefundRepository::class)]
#[ApiResource(
    attributes: [
        "force_eager" => false,
        'normalization_context' => ['groups' => ['refund:read'], "enable_max_depth" => true],
        'denormalization_context' => ['groups' => ['refund:write'], "enable_max_depth" => true],
    ],
)]
class Refund
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(["group:read", 'refund:read'])]
    private $price;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'refunds')]
    #[ORM\JoinColumn(nullable: false)]
    private $refundGroup;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'refunds')]
    #[Groups(["group:read", 'refund:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private $refunder;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'refundsReceiver')]
    #[Groups(["group:read", 'refund:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private $receiver;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRefundGroup(): ?Group
    {
        return $this->refundGroup;
    }

    public function setRefundGroup(?Group $refundGroup): self
    {
        $this->refundGroup = $refundGroup;

        return $this;
    }

    public function getRefunder(): ?User
    {
        return $this->refunder;
    }

    public function setRefunder(?User $refunder): self
    {
        $this->refunder = $refunder;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}

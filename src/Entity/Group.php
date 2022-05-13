<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiSubresource;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    attributes: [
        "force_eager" => false,
        'normalization_context' => ['groups' => ['group:read']],
        'denormalization_context' => ['groups' => ['group:write']],
    ],
)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["group:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["group:read", "group:write", "user:read"])]
    private $name;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups', cascade: ['persist'])]
    #[Groups(["user:write", "group:write", "group:read"])]
    private $members;

    #[ORM\OneToMany(mappedBy: 'expenseGroup', targetEntity: Expense::class)]
    #[Groups(["expense:write", "group:write", "group:read"])]
    #[ApiSubresource]
    private $expenses;

    #[ORM\ManyToOne(targetEntity: GroupType::class, inversedBy: 'groups')]
    #[Groups(["group:read", "group:write", "group_type:read"])]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["group:read", "group:write"])]
    private $code;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->expenses = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses[] = $expense;
            $expense->setExpenseGroup($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getExpenseGroup() === $this) {
                $expense->setExpenseGroup(null);
            }
        }

        return $this;
    }

    public function getType(): ?GroupType
    {
        return $this->type;
    }

    public function setType(?GroupType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}

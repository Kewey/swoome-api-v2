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
        'normalization_context' => ['groups' => ['group:read']],
        'denormalization_context' => ['groups' => ['group:write']],
    ],
    collectionOperations: [
        'post',
        'get' => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Désolé, vous devez être admin pour voir tous les groupes.",
        ],
    ],
)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["group:read", "user:read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["group:read", "group:write", "user:read"])]
    private $name;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups', cascade: ['persist'])]
    #[Groups(["group:write", "group:read"])]
    private $members;

    #[ORM\OneToMany(mappedBy: 'expenseGroup', targetEntity: Expense::class, cascade: ['persist', 'remove'])]
    #[Groups(["group:write", "group:read"])]
    #[ApiSubresource]
    private $expenses;

    #[ORM\ManyToOne(targetEntity: GroupType::class, inversedBy: 'groups')]
    #[Groups(["group:read", "group:write", "group_type:read", "user:read"])]
    #[ORM\JoinColumn(nullable: false)]
    private $type;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["group:read", "group:write"])]
    private $code;

    #[ORM\OneToMany(mappedBy: 'refundGroup', targetEntity: Refund::class, orphanRemoval: true, cascade: ['persist'])]
    #[ApiSubresource]
    #[Groups(["refund:write", "group:write", "group:read"])]
    private $refunds;

    #[ORM\OneToMany(mappedBy: 'balanceGroup', targetEntity: Balance::class, orphanRemoval: true, cascade: ["persist"])]
    #[ApiSubresource]
    #[Groups(["balance:write", "group:write", "group:read"])]
    private $balances;

    #[ORM\ManyToMany(targetEntity: ExpenseType::class, mappedBy: 'groups')]
    #[Groups(["group:read", "group:write", "expense_type:read"])]
    private $expenseTypes;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->refunds = new ArrayCollection();
        $this->balances = new ArrayCollection();
        $this->expenseTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
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

    public function getMembers()
    {
        return $this->members->getValues();
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

    /**
     * @return Collection<int, Refund>
     */
    public function getRefunds(): Collection
    {
        return $this->refunds;
    }

    public function addRefund(Refund $refund): self
    {
        if (!$this->refunds->contains($refund)) {
            $this->refunds[] = $refund;
            $refund->setRefundGroup($this);
        }

        return $this;
    }

    public function removeRefund(Refund $refund): self
    {
        if ($this->refunds->removeElement($refund)) {
            // set the owning side to null (unless already changed)
            if ($refund->getRefundGroup() === $this) {
                $refund->setRefundGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Balance>
     */
    public function getBalances(): Collection
    {
        return $this->balances;
    }

    public function addBalance(Balance $balance): self
    {
        if (!$this->balances->contains($balance)) {
            $this->balances[] = $balance;
            $balance->setBalanceGroup($this);
        }

        return $this;
    }

    public function removeBalance(Balance $balance): self
    {
        if ($this->balances->removeElement($balance)) {
            // set the owning side to null (unless already changed)
            if ($balance->getBalanceGroup() === $this) {
                $balance->setBalanceGroup(null);
            }
        }

        return $this;
    }

    #[Groups("group:read")] // <- MAGIC IS HERE, you can set a group on a method.
    public function getSumExpenses(): int
    {
        $sumExpenses = 0;
        foreach ($this->getExpenses() as $expense) {
            if ($expense->getType()) {
                if ($expense->getType()->getName() != 'Remboursement') {
                    $sumExpenses += $expense->getPrice();
                }
            } else {
                $sumExpenses += $expense->getPrice();
            }
        }
        return $sumExpenses;
    }

    /**
     * @return Collection<int, ExpenseType>
     */
    public function getExpenseTypes(): Collection
    {
        return $this->expenseTypes;
    }

    public function addExpenseType(ExpenseType $expenseType): self
    {
        if (!$this->expenseTypes->contains($expenseType)) {
            $this->expenseTypes[] = $expenseType;
            $expenseType->addGroup($this);
        }

        return $this;
    }

    public function removeExpenseType(ExpenseType $expenseType): self
    {
        if ($this->expenseTypes->removeElement($expenseType)) {
            $expenseType->removeGroup($this);
        }

        return $this;
    }
}

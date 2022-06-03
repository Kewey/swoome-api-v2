<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\ExpenseTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseTypeRepository::class)]
#[ApiResource(attributes: [
    'normalization_context' => ['groups' => ['expense_type:read']],
    'denormalization_context' => ['groups' => ['expense_type:write']],
],)]
class ExpenseType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(["expense_type:read", "group:read"])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(["expense_type:read", "expense_type:write", "expense:read", "group:read"])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[Groups(["expense_type:read", "expense_type:write", "expense:read", "group:read"])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $emoji;

    #[ApiSubresource]
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Expense::class)]
    private $expense;

    #[Groups(["expense_type:read", "expense_type:write", "expense:read", "group:read"])]
    #[ORM\Column(type: 'boolean')]
    private $isDefault = false;

    #[ORM\ManyToMany(targetEntity: GroupType::class, inversedBy: 'expenseTypes')]
    private $groupTypes;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'expenseTypes')]
    private $groups;

    public function __construct()
    {
        $this->expense = new ArrayCollection();
        $this->groupTypes = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(?string $emoji): self
    {
        $this->emoji = $emoji;

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpense(): Collection
    {
        return $this->expense;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expense->contains($expense)) {
            $this->expense[] = $expense;
            $expense->setType($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expense->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getType() === $this) {
                $expense->setType(null);
            }
        }

        return $this;
    }

    public function isIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return Collection<int, GroupType>
     */
    public function getGroupTypes(): Collection
    {
        return $this->groupTypes;
    }

    public function addGroupType(GroupType $groupType): self
    {
        if (!$this->groupTypes->contains($groupType)) {
            $this->groupTypes[] = $groupType;
        }

        return $this;
    }

    public function removeGroupType(GroupType $groupType): self
    {
        $this->groupTypes->removeElement($groupType);

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\GroupTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupTypeRepository::class)]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['group_type:read']],
        'denormalization_context' => ['groups' => ['group_type:write']],
    ],
)]
class GroupType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(["group_type:read"])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(["group_type:read", "group_type:write", "group:read", "user:read"])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[Groups(["group_type:read", "group_type:write", "group:read", "user:read"])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $emoji;

    #[ApiSubresource]
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Group::class)]
    private $groups;

    #[ORM\ManyToMany(targetEntity: ExpenseType::class, mappedBy: 'groupTypes')]
    #[Groups(["group_type:read", "group_type:write"])]
    private $expenseTypes;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->expenseTypes = new ArrayCollection();
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
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setType($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getType() === $this) {
                $group->setType(null);
            }
        }

        return $this;
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
            $expenseType->addGroupType($this);
        }

        return $this;
    }

    public function removeExpenseType(ExpenseType $expenseType): self
    {
        if ($this->expenseTypes->removeElement($expenseType)) {
            $expenseType->removeGroupType($this);
        }

        return $this;
    }
}

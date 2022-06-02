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
    #[Groups(["expense_type:read"])]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(["expense_type:read", "expense_type:write", "expense:read"])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[Groups(["expense_type:read", "expense_type:write", "expense:read"])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $emoji;

    #[ApiSubresource]
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Expense::class)]
    private $expense;

    public function __construct()
    {
        $this->expense = new ArrayCollection();
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
}

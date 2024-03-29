<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\GetCurrentUserController;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['user:read']],
        'denormalization_context' => ['groups' => ['user:write']],
    ],
    collectionOperations: [
        'post' => [
            "method" => "POST",
            'path' => '/auth/register',
            "validation_groups" => ["Default", "create"],
        ],
        'get' => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Désolé, vous devez être admin pour voir tous les utilisateurs.",
        ],
    ],
    itemOperations: [
        'get',
        "get_me" => [
            "method" => "GET",
            "path" => "/me",
            "controller" => GetCurrentUserController::class,
            "openapi_context" => [
                "parameters" => []
            ],
            "read" => false
        ],
        'put' => [
            "security" => "is_granted('ROLE_ADMIN') or object == user",
            "security_message" => "Désolé, vous ne pouvez pas éditer un autre utilisateur.",
        ],
        'delete' => [
            "security" => "is_granted('ROLE_ADMIN') or object == user",
            "security_message" => "Désolé, vous ne pouvez pas supprimer un autre utilisateur.",
        ],
    ]

)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user:read", "group:read", "expense:read"])]
    private $id;

    #[Groups(["user:read", "user:write"])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(["user:read", "user:write"])]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[SerializedName("password")]
    #[Groups(["user:write"])]
    #[Assert\NotBlank(["groups" => ["create"]])]
    private $plainPassword;

    #[Groups(["user:read", "user:write", "group:read", "expense:read", 'balance:read', 'refund:read'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[Groups(["user:read",  "user:write"])]
    #[ApiSubresource]
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members', cascade: ["persist"])]
    private $groups;

    #[ORM\OneToMany(mappedBy: 'madeBy', targetEntity: Expense::class, cascade: ['persist', 'remove'])]
    #[Groups(["user:write"])]
    #[ApiSubresource]
    private $createdExpenses;

    #[ORM\ManyToMany(targetEntity: Expense::class, orphanRemoval: true, mappedBy: 'participants', cascade: ['remove'])]
    #[Groups(["user:write"])]
    private $participatedExpenses;

    #[ORM\OneToMany(mappedBy: 'balanceUser', targetEntity: Balance::class, cascade: ['persist', 'remove'])]
    private $balances;

    #[ORM\OneToMany(mappedBy: 'refunder', targetEntity: Refund::class, orphanRemoval: false, cascade: ['remove'])]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $refunds;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Refund::class, orphanRemoval: true)]
    private $refundsReceiver;

    #[ORM\Column(type: 'boolean')]
    #[Groups(["user:read"])]
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    private $pushToken;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Media::class, cascade: ['persist', 'remove'])]
    #[Groups(["user:read", "user:write", "media:read", "group:read", "expense:read", "balance:read", "refund:read"])]
    private $avatar;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->createdExpenses = new ArrayCollection();
        $this->participatedExpenses = new ArrayCollection();
        $this->balances = new ArrayCollection();
        $this->refunds = new ArrayCollection();
        $this->refundsReceiver = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
            $group->addMember($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getCreatedExpenses(): Collection
    {
        return $this->createdExpenses;
    }

    public function addExpense(Expense $createdExpense): self
    {
        if (!$this->createdExpenses->contains($createdExpense)) {
            $this->createdExpenses[] = $createdExpense;
            $createdExpense->setMadeBy($this);
        }

        return $this;
    }

    public function removeExpense(Expense $createdExpense): self
    {
        if ($this->createdExpenses->removeElement($createdExpense)) {
            // set the owning side to null (unless already changed)
            if ($createdExpense->getMadeBy() === $this) {
                $createdExpense->setMadeBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getParticipatedExpenses(): Collection
    {
        return $this->participatedExpenses;
    }

    public function addParticipatedExpense(Expense $participatedExpense): self
    {
        if (!$this->participatedExpenses->contains($participatedExpense)) {
            $this->participatedExpenses[] = $participatedExpense;
            $participatedExpense->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipatedExpense(Expense $participatedExpense): self
    {
        if ($this->participatedExpenses->removeElement($participatedExpense)) {
            $participatedExpense->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Balance[]
     */
    public function getBalances(): Collection
    {
        return $this->balances;
    }

    public function addBalance(Balance $balance): self
    {
        if (!$this->balances->contains($balance)) {
            $this->balances[] = $balance;
            $balance->setBalanceUser($this);
        }

        return $this;
    }

    public function removeBalance(Balance $balance): self
    {
        if ($this->balances->removeElement($balance)) {
            // set the owning side to null (unless already changed)
            if ($balance->getBalanceUser() === $this) {
                $balance->setBalanceUser(null);
            }
        }

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
            $refund->setRefunder($this);
        }

        return $this;
    }

    public function removeRefund(Refund $refund): self
    {
        if ($this->refunds->removeElement($refund)) {
            // set the owning side to null (unless already changed)
            if ($refund->getRefunder() === $this) {
                $refund->setRefunder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Refund>
     */
    public function getRefundsReceiver(): Collection
    {
        return $this->refundsReceiver;
    }

    public function addRefundsReceiver(Refund $refundsReceiver): self
    {
        if (!$this->refundsReceiver->contains($refundsReceiver)) {
            $this->refundsReceiver[] = $refundsReceiver;
            $refundsReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeRefundsReceiver(Refund $refundsReceiver): self
    {
        if ($this->refundsReceiver->removeElement($refundsReceiver)) {
            // set the owning side to null (unless already changed)
            if ($refundsReceiver->getReceiver() === $this) {
                $refundsReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPushToken(): ?string
    {
        return $this->pushToken;
    }

    public function setPushToken(?string $pushToken): self
    {
        $this->pushToken = $pushToken;

        return $this;
    }

    public function getAvatar(): ?Media
    {
        return $this->avatar;
    }

    public function setAvatar(?Media $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}

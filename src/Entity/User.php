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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    attributes: [
        "force_eager" => false,
        'normalization_context' => ['groups' => ['user:read'], "enable_max_depth" => true],
        'denormalization_context' => ['groups' => ['user:write'], "enable_max_depth" => true],
    ],
    collectionOperations: [
        'post' => [
            "method" => "POST",
            'path' => '/auth/register',
            "validation_groups" => ["Default", "create"]
        ],
        'get',
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
        'put',
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
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[SerializedName("password")]
    #[Groups(["user:write"])]
    #[Assert\NotBlank(["groups" => ["create"]])]
    private $plainPassword;

    #[Groups(["user:read", "user:write", "group:read", "expense:read"])]
    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[Groups(["user:read", "user:write"])]
    #[ApiSubresource]
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'members')]
    private $groups;

    #[ORM\OneToMany(mappedBy: 'madeBy', targetEntity: Expense::class)]
    #[Groups(["user:read", "user:write"])]
    #[ApiSubresource]
    private $createdExpenses;

    #[ORM\ManyToMany(targetEntity: Expense::class, mappedBy: 'participants')]
    #[Groups(["user:read", "user:write"])]
    private $participatedExpenses;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->createdExpenses = new ArrayCollection();
        $this->participatedExpenses = new ArrayCollection();
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
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ApiResource(attributes: [
    'normalization_context' => ['groups' => ['media:read']],
    'denormalization_context' => ['groups' => ['media:write']],
],)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["user:read", "user:write", 'media:read', 'media:write'])]
    private $url;

    #[ORM\OneToOne(inversedBy: 'avatar', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[Groups(['media:read', 'media:write'])]
    private $userAvatar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUserAvatar(): ?User
    {
        return $this->userAvatar;
    }

    public function setUserAvatar(?User $userAvatar): self
    {
        $this->userAvatar = $userAvatar;

        return $this;
    }
}

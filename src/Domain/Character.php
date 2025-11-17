<?php

namespace App\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity]
#[ORM\Table(name: 'characters')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Groups('api')]
    #[SerializedName('name')]
    #[ORM\Column(type: Types::STRING, length: 150)]
    private string $name;

    #[Groups('api')]
    #[SerializedName('gender')]
    #[ORM\Column(type: Types::ENUM, enumType: Gender::class)]
    private Gender $gender;

    #[Groups('api')]
    #[SerializedName('status')]
    #[ORM\Column(type: Types::ENUM, enumType: CharacterStatus::class)]
    private CharacterStatus $status;

    #[Groups('api')]
    #[SerializedName('url')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $url;

    public function __construct(string $name, Gender $gender, CharacterStatus $characterStatus, ?string $url)
    {
        $this->name = $name;
        $this->gender = $gender;
        $this->status = $characterStatus;
        $this->url = $url;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getStatus(): CharacterStatus
    {
        return $this->status;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}

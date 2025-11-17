<?php

namespace App\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity]
#[ORM\Table(name: 'reviews')]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Groups('api')]
    #[SerializedName('author')]
    #[ORM\Column(type: Types::STRING, length: 150)]
    private string $author;

    #[Groups('api')]
    #[SerializedName('text')]
    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[Groups('api')]
    #[SerializedName('publication_date')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeInterface $publicationDate;

    #[Groups('api')]
    #[SerializedName('rating')]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $rating;

    #[ORM\ManyToOne(targetEntity: Episode::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Episode $episode = null;

    public function __construct(string $author, string $text, int $rating, Episode $episode, \DateTimeImmutable $publicationDate = new \DateTimeImmutable())
    {
        $this->author = $author;
        $this->text = $text;
        $this->publicationDate = $publicationDate;
        $this->rating = $rating;
        $this->episode = $episode;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getPublicationDate(): \DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

}

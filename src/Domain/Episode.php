<?php

namespace App\Domain;

use App\Infrastructure\Persistence\EpisodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
#[ORM\Table(name: 'episodes')]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Groups('api')]
    #[SerializedName('title')]
    #[ORM\Column(type: Types::STRING, length: 200)]
    private string $title;

    #[Groups('api')]
    #[SerializedName('release_date')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeInterface $releaseDate;

    #[Groups('api')]
    #[SerializedName('season')]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $season;

    #[Groups('api')]
    #[SerializedName('series')]
    #[ORM\Column(type: Types::SMALLINT)]
    private int $series;

    /**
     * @var Collection<Review>
     */
    #[Groups('api')]
    #[SerializedName('reviews')]
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'episode', cascade: ['persist', 'remove'])]
    private Collection $reviews;

    /**
     * @var Collection<Character>
     */
    #[Groups('api')]
    #[SerializedName('characters')]
    #[ORM\ManyToMany(targetEntity: Character::class, cascade: ['persist', 'remove'])]
    private Collection $characters;

    public function __construct(string $title, int $season, int $series, \DateTimeImmutable $releaseDate)
    {
        $this->title = $title;
        $this->season = $season;
        $this->series = $series;
        $this->releaseDate = $releaseDate;
        $this->reviews = new ArrayCollection();
        $this->characters = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function addCharacter(Character $character): void
    {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
        }
    }

    public function addReview(string $author, string $text, int $rating, \DateTimeImmutable $publicationDate = new \DateTimeImmutable()): void
    {
        $review = new Review($author, $text, $rating, $this, $publicationDate);
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseDate(): \DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function getSeries(): int
    {
        return $this->series;
    }

    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }
}

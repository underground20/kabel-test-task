<?php

namespace App\App\Import\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'imports')]
class Import
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::ENUM, enumType: ImportType::class)]
    private ImportType $type;

    #[ORM\Column(type: Types::INTEGER)]
    private int $lastPage;

    #[ORM\Column(type: Types::INTEGER)]
    private int $lastId;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $importedCount;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeInterface $date;

    public function __construct(
        ImportType $type,
        int $lastPage,
        int $lastId,
        int $importedCount,
        \DateTimeImmutable $date = new \DateTimeImmutable()
    ) {
        $this->type = $type;
        $this->lastPage = $lastPage;
        $this->lastId = $lastId;
        $this->importedCount = $importedCount;
        $this->date = $date;
    }

    public function getType(): ImportType
    {
        return $this->type;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getLastId(): int
    {
        return $this->lastId;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}

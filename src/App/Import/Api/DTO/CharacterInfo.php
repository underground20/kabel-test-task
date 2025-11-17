<?php

namespace App\App\Import\Api\DTO;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class CharacterInfo
{
    #[Type('integer')]
    public int $id;

    #[Type('string')]
    public string $name;

    #[Type('string')]
    public string $status;

    #[Type('string')]
    public string $gender;

    #[Type('string')]
    public string $url;

    /** @var array<string> */
    #[Type('array<string>')]
    #[Serializer\SerializedName('episode')]
    public array $episodes;
}

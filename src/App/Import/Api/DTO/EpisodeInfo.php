<?php

namespace App\App\Import\Api\DTO;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class EpisodeInfo
{
    #[Type('integer')]
    public int $id;

    #[Type('string')]
    public string $name;

    #[Type('string')]
    #[Serializer\SerializedName('air_date')]
    public string $releaseDate;

    #[Type('string')]
    public string $episode;

    #[Type('string')]
    public string $url;
}

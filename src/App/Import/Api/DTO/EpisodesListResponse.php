<?php

namespace App\App\Import\Api\DTO;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

final class EpisodesListResponse
{
    #[Type(Info::class)]
    #[SerializedName('info')]
    public Info $info;

    /** @var array<EpisodeInfo> */
    #[Type('array<App\App\Import\Api\DTO\EpisodeInfo>')]
    #[SerializedName('results')]
    public array $results = [];

    public function __construct(Info $info, array $results = [])
    {
    }
}

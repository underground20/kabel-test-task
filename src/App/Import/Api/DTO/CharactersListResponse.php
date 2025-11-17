<?php

namespace App\App\Import\Api\DTO;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

final class CharactersListResponse
{
    #[Type(Info::class)]
    #[SerializedName('info')]
    public Info $info;

    /** @var array<CharacterInfo> */
    #[Type('array<App\App\Import\Api\DTO\CharacterInfo>')]
    #[SerializedName('results')]
    public array $results = [];

    public function __construct(Info $info, array $results = [])
    {
        $this->info = $info;
        $this->results = $results;
    }
}

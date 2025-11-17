<?php

namespace App\App\Import\Api\DTO;

use JMS\Serializer\Annotation\Type;

class Info
{
    #[Type('integer')]
    public int $count;

    #[Type('integer')]
    public int $pages;

    #[Type('string')]
    public ?string $next;

    #[Type('string')]
    public ?string $prev;

    public function __construct(int $count = 0, int $pages = 0, ?string $next = null, ?string $prev = null)
    {
    }
}

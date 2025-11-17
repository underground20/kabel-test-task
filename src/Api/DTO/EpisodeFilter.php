<?php

namespace App\Api\DTO;

final class EpisodeFilter
{
    public function __construct(
        public ?int $season,
        public ?\DateTimeImmutable $from,
        public ?\DateTimeImmutable $to,
    ) {
    }
}

<?php

namespace App\App\Import;

use App\Domain\CharacterStatus;
use App\Domain\Gender;

final class Mapper
{
    public static function mapGender(string $gender): Gender
    {
        return match ($gender) {
            'Male' => Gender::Male,
            'Female' => Gender::Female,
            default => Gender::Unknown,
        };
    }

    public static function mapStatus(string $status): CharacterStatus
    {
        return match ($status) {
            'Alive' => CharacterStatus::Alive,
            'Dead' => CharacterStatus::Dead,
            default => CharacterStatus::Unknown,
        };
    }

    /** @return array{0: int, 1: int} */
    public static function mapSeasonAndSeries(string $episodeCode): array
    {
        $pattern = '/^S(\d+)E(\d+)$/';
        if (preg_match($pattern, $episodeCode, $matches)) {
            return [(int)$matches[1], (int)$matches[2]];
        }

        return [0, 0];
    }
}

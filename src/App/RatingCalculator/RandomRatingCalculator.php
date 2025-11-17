<?php

namespace App\App\RatingCalculator;

final class RandomRatingCalculator implements RatingCalculator
{
    public function calculate(string $text): int
    {
        return random_int(1, 5);
    }
}

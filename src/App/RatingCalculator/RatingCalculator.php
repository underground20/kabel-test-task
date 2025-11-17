<?php

namespace App\App\RatingCalculator;

interface RatingCalculator
{
    public function calculate(string $text): int;
}

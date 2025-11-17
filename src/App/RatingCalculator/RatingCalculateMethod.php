<?php

namespace App\App\RatingCalculator;

enum RatingCalculateMethod: string
{
    case Random = 'random';
    case Sentiment = 'sentiment';
}

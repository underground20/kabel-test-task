<?php

namespace App\App\RatingCalculator;

use Sentiment\Analyzer;

final class SentimentRatingCalculator implements RatingCalculator
{
    private Analyzer $analyzer;

    public function __construct()
    {
        $this->analyzer = new Analyzer();
    }

    public function calculate(string $text): int
    {
        $sentiment = $this->analyzer->getSentiment($text);

        return $this->convertCompoundToRating($sentiment['compound']);
    }

    private function convertCompoundToRating(float $compound): int
    {
        $compound = max(-1, min(1, $compound));
        $rating = 1 + ($compound + 1) / 2 * 4;

        return (int) round($rating);
    }
}

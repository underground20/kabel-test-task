<?php

namespace App\App\RatingCalculator;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class RatingCalculatorFactory
{
    public function __construct(
        #[Autowire(env: 'RATING_CALCULATE_METHOD')]
        private string $ratingCalculateMethod
    ) {
    }

    /** @throws \InvalidArgumentException */
    public function create(): RatingCalculator
    {
        $method = RatingCalculateMethod::tryFrom($this->ratingCalculateMethod);

        return match ($method) {
            RatingCalculateMethod::Random => new RandomRatingCalculator(),
            RatingCalculateMethod::Sentiment => new SentimentRatingCalculator(),
            default => throw new \InvalidArgumentException('Unrecognized rating calculate method'),
        };
    }
}

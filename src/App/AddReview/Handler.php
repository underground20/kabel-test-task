<?php

namespace App\App\AddReview;

use App\App\RatingCalculator\RatingCalculatorFactory;
use App\Domain\Episode;
use Doctrine\ORM\EntityManagerInterface;

final readonly class Handler
{
    public function __construct(
        private RatingCalculatorFactory $ratingCalculatorFactory,
        private EntityManagerInterface $em,
    ) {
    }

    public function addReview(Episode $episode, ReviewInput $input): void
    {
        $calculator = $this->ratingCalculatorFactory->create();
        $episode->addReview(
            $input->author,
            $input->text,
            $calculator->calculate($input->text),
        );
        $this->em->flush();
    }
}

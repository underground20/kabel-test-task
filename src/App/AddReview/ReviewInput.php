<?php

namespace App\App\AddReview;

use Symfony\Component\Validator\Constraints as Assert;

final class ReviewInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 150)]
        public string $author,
        #[Assert\NotBlank]
        #[Assert\Length(min: 20)]
        public string $text,
    ) {
    }
}

<?php

namespace App\App\Import;

use App\App\RatingCalculator\RatingCalculatorFactory;
use Doctrine\DBAL\Connection;
use Faker\Factory;
use Random\Randomizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('import:reviews')]
final class ImportReviewsCommand extends Command
{
    public function __construct(
        private readonly RatingCalculatorFactory $ratingCalculatorFactory,
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Start import reviews to episodes</info>");

        $resource = fopen(__DIR__ . '/../../../resource/rick_and_morty_reviews_50000_clean.json', 'rb');
        if (!$resource) {
            $output->writeln("<error>Could not open resource file</error>");

            return Command::FAILURE;
        }

        try {
            $calculator = $this->ratingCalculatorFactory->create();
        } catch (\InvalidArgumentException) {
            $output->writeln("<error>Invalid rating calculate method in configuration</error>");

            return Command::FAILURE;
        }

        $episodesIds = $this->getEpisodesIds();
        if (empty($episodesIds)) {
            $output->writeln("<info>Nothing to import. Empty episodes list</info>");

            return Command::SUCCESS;
        }

        $faker = Factory::create();
        $randomizer = new Randomizer();
        $number = $randomizer->getInt(50, 500);
        $addedCount = 0;

        $episodeId = array_shift($episodesIds);
        $reviews = [];
        while (($line = fgets($resource)) !== false) {
            $trimmedText = trim($line);
            if ($trimmedText === '' || $trimmedText === '[' || $trimmedText === ']') {
                continue;
            }

            $trimmedText = $this->trimRow($trimmedText);

            $publicationDate = (new \DateTimeImmutable())
                ->modify('-' . $randomizer->getInt(0, 365) . ' days')
                ->format('Y-m-d H:i:s')
            ;
            $reviews[] = [
                'author' => $faker->name(),
                'text' => $trimmedText,
                'publication_date' => $publicationDate,
                'rating' => $calculator->calculate($trimmedText),
                'episode_id' => $episodeId,
            ];
            $addedCount++;

            if ($addedCount >= $number) {
                $this->addReviews($reviews);
                $reviews = [];
                $number = $randomizer->getInt(50, 500);
                $addedCount = 0;
                $episodeId = array_shift($episodesIds);
                if ($episodeId === null) {
                    fclose($resource);
                    break;
                }
            }
        }

        if (!empty($reviews)) {
            $this->addReviews($reviews);
        }

        $output->writeln("<info>End import reviews to episodes</info>");
        $output->writeln('Memory peak usage (MB): ' . memory_get_peak_usage(true) / 1024 / 1024);

        return Command::SUCCESS;
    }

    private function trimRow(string $text): string
    {
        if (str_ends_with($text, ',')) {
            $text = substr($text, 0, -1);
        }

        if (str_starts_with($text, '"') && str_ends_with($text, '"')) {
            $text = substr($text, 1, -1);
        }

        return $text;
    }

    /** @return array<int> */
    private function getEpisodesIds(): array
    {
        $stmt = $this->connection->executeQuery(
            <<<SQL
                SELECT DISTINCT e.id 
                FROM episodes e
                LEFT JOIN reviews r on e.id = r.episode_id
                WHERE r.episode_id IS NULL
                ORDER BY id
            SQL
        );
        $data = $stmt->fetchAllAssociative();

        return array_column($data, 'id');
    }

    /** @param array<string, string|int> $reviews */
    private function addReviews(array $reviews): void
    {
        $values = [];
        foreach ($reviews as $review) {
            $values[] = sprintf(
                "(%s, %s, %s, %f, %d)",
                $this->connection->quote($review['author']),
                $this->connection->quote($review['text']),
                $this->connection->quote($review['publication_date']),
                $review['rating'],
                $review['episode_id']
            );
        }

        $this->connection->executeStatement(
            sprintf(
                'INSERT INTO reviews (author, text, publication_date, rating, episode_id) VALUES %s',
                implode(', ', $values)
            )
        );
    }
}

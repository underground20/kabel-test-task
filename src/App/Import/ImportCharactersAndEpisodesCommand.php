<?php

namespace App\App\Import;

use App\App\Import\Api\Api;
use App\App\Import\Api\DTO\CharacterInfo;
use App\App\Import\Api\DTO\EpisodeInfo;
use App\App\Import\Model\Import;
use App\App\Import\Model\ImportType;
use App\Domain\Character;
use App\Domain\Episode;
use App\Infrastructure\Persistence\ImportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('import:characters-and-episodes')]
final class ImportCharactersAndEpisodesCommand extends Command
{
    public function __construct(
        private readonly Api $api,
        private readonly EntityManagerInterface $em,
        private readonly ImportRepository $importRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Start import episodes and characters</info>");

        $urlsToEpisodes = $this->importEpisodes($output);
        $episodeUrlsToCharacters = $this->importCharacters($output);
        $this->addCharactersInEpisodes($urlsToEpisodes, $episodeUrlsToCharacters, $output);

        $output->writeln("<info>End import episodes and characters</info>");
        $output->writeln('Memory peak usage (MB): ' . memory_get_peak_usage(true) / 1024 / 1024);

        return Command::SUCCESS;
    }

    /** @return array<string, Episode> */
    private function importEpisodes(OutputInterface $output): array
    {
        $import = $this->importRepository->findLastByType(ImportType::Episode);
        $page = $import?->getLastPage() ?: 1;
        $id = 1;
        $urlsToEpisodes = [];
        do {
            $episodesListResponse = $this->api->getEpisodes($page);
            $page++;
            if (empty($episodesListResponse->results)) {
                break;
            }

            if ($import !== null && $import->getImportedCount() === $episodesListResponse->info->count) {
                break;
            }

            foreach ($episodesListResponse->results as $episodeDTO) {
                if ($import !== null && $episodeDTO->id <= $import->getLastId()) {
                    continue;
                }

                $episode = $this->createEpisode($episodeDTO);
                $urlsToEpisodes[$episodeDTO->url] = $episode;
                $id = $episodeDTO->id;
            }

        } while ($episodesListResponse->info->next !== null);

        if (!empty($urlsToEpisodes)) {
            $import = new Import(ImportType::Episode, $page - 1, $id, count($urlsToEpisodes));
            $this->em->persist($import);
            $this->em->flush();
        }

        $count = count($urlsToEpisodes);
        $output->writeln("<info>$count episodes were imported</info>");

        return $urlsToEpisodes;
    }

    /** @return array<string, array<Character>> */
    private function importCharacters(OutputInterface $output): array
    {
        $import = $this->importRepository->findLastByType(ImportType::Character);
        $page = $import?->getLastPage() ?: 1;
        $id = 1;
        $charactersCount = 0;
        $episodeUrlsToCharacters = [];
        do {
            $charactersListResponse = $this->api->getCharacters($page);
            $page++;
            if (empty($charactersListResponse->results)) {
                break;
            }

            if ($import !== null && $import->getImportedCount() === $charactersListResponse->info->count) {
                break;
            }

            foreach ($charactersListResponse->results as $characterDTO) {
                if ($import !== null && $characterDTO->id <= $import->getLastId()) {
                    continue;
                }

                $charactersCount++;
                $character = $this->createCharacter($characterDTO);
                $id = $characterDTO->id;
                foreach ($characterDTO->episodes as $episodeUrl) {
                    $episodeUrlsToCharacters[$episodeUrl][] = $character;
                }
            }

        } while ($charactersListResponse->info->next !== null);

        if (!empty($episodeUrlsToCharacters)) {
            $import = new Import(ImportType::Character, $page - 1, $id, $charactersCount);
            $this->em->persist($import);
            $this->em->flush();
        }

        $output->writeln("<info>$charactersCount characters were imported</info>");

        return $episodeUrlsToCharacters;
    }

    /** @param array<string, Episode> $urlsToEpisodes
     * @param array<string, array<Character>> $episodeUrlsToCharacters
     **/
    private function addCharactersInEpisodes(array $urlsToEpisodes, array $episodeUrlsToCharacters, OutputInterface $output): void
    {
        foreach ($urlsToEpisodes as $url => $episode) {
            $output->writeln("Episode '$episode' - add characters");
            $characters = $episodeUrlsToCharacters[$url] ?? [];
            if (empty($characters)) {
                continue;
            }

            foreach ($characters as $character) {
                $output->writeln("Add character '$character'");
                $episode->addCharacter($character);
                $this->em->persist($episode);
            }
        }

        if (!empty($urlsToEpisodes)) {
            $this->em->flush();
        }
    }

    private function createEpisode(EpisodeInfo $episodeInfo): Episode
    {
        [$season, $series] = Mapper::mapSeasonAndSeries($episodeInfo->episode);
        $releaseDate = \DateTimeImmutable::createFromFormat('F j, Y', $episodeInfo->releaseDate);

        $episode = new Episode($episodeInfo->name, $season, $series, $releaseDate);
        $this->em->persist($episode);

        return $episode;
    }

    private function createCharacter(CharacterInfo $characterInfo): Character
    {
        $character = new Character(
            $characterInfo->name,
            Mapper::mapGender($characterInfo->gender),
            Mapper::mapStatus($characterInfo->status),
            $characterInfo->url,
        );
        $this->em->persist($character);

        return $character;
    }
}

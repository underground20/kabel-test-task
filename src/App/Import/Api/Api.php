<?php

namespace App\App\Import\Api;

use App\App\Import\Api\DTO\CharactersListResponse;
use App\App\Import\Api\DTO\EpisodesListResponse;
use App\App\Import\Api\DTO\Info;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

final class Api
{
    private const string URL = 'https://rickandmortyapi.com/api';

    private Client $client;

    public function __construct(private SerializerInterface $serializer, private LoggerInterface $logger)
    {
        $this->client = new Client();
    }

    public function getCharacters(int $page = 1): CharactersListResponse
    {
        try {
            $rawResponse = $this->client->request('GET', self::URL . "/character?page=$page");

            return $this->serializer->deserialize($rawResponse->getBody()->getContents(), CharactersListResponse::class, 'json');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return new CharactersListResponse(new Info());
        }
    }

    public function getEpisodes(int $page = 1): EpisodesListResponse
    {
        try {
            $rawResponse = $this->client->request('GET', self::URL . "/episode?page=$page");
            $response = $this->serializer->deserialize($rawResponse->getBody()->getContents(), EpisodesListResponse::class, 'json');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return new EpisodesListResponse(new Info());
        }

        return $response;
    }
}

<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Movie
{
    /** @var HttpClientInterface */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    private $defaultOptions;
    private $defaultResponse;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = $client->withOptions([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJmMjRiNGViYjcwYjAzMzJlY2RiM2Y3ZTUzOTE0YjlkYyIsInN1YiI6IjYxNjU3Yzc1NjJmY2QzMDAyYjRiMWIwNyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.dmRxBwmXWRNALxobA3mo3aa6D0p_pOGRlGpdjDKnXHY',
                'Content-Type' => 'application/json;charset=utf-8'
            ]
        ]);
        $this->defaultOptions = [
            'query' => [
                'append_to_response' => 'videos,images'
            ]
        ];
        $this->defaultResponse = [
            'error' => true,
            'msg' => ''
        ];
    }

    public function getConfiguration(): array
    {
        try {
            $response = $this->client->request( 'GET', "configuration");
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO" !');
            }
            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getConfiguration error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }

    public function getMovie(int $idMovie): array
    {
        try {
            $response = $this->client->request( 'GET', "movie/{$idMovie}", $this->defaultOptions);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO !');
            }
            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getMovie error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }

    public function getPopularMovies(): array
    {
        try {
            $response = $this->client->request( 'GET', "movie/popular", $this->defaultOptions);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO !');
            }

            return ($response->toArray())['results'];
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getPopularMovies error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }

    public function getGenres(): array
    {
        try {
            $response = $this->client->request( 'GET', "genre/movie/list");
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO" !');
            }
            return ($response->toArray())['genres'];
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getGenres error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }
}
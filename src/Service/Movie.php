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

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, string $apiUrl, string $apiKey)
    {
        $this->logger = $logger;

        $this->client = $client->withOptions([
            'base_uri' => $apiUrl,
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
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

    public function searchMovie(string $query, int $page = 1): string
    {
        try {
            $this->defaultOptions['query']['query'] = \urlencode($query);
            $this->defaultOptions['query']['page'] = $page;

            $response = $this->client->request( 'GET', "search/movie", $this->defaultOptions);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO !');
            }
            return $response->getContent();
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getMovie error : %s', $e->getMessage()));
            return \json_encode(array_merge($this->defaultResponse, ['msg' => $e->getMessage()]));
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

    public function discoverMovie(array $filters): array
    {
        try {
            if (empty($filters)) {
                return [];
            }

            foreach($filters as $param => $value) {
                $this->defaultOptions['query'][$param] = $value;
            }
            $response = $this->client->request( 'GET', "discover/movie", $this->defaultOptions);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO" !');
            }

            return ($response->toArray())['results'];
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('discoverMovie error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }

    public function getVideo(int $idMovie):array
    {
        try {
            $response = $this->client->request( 'GET', "movie/{$idMovie}/videos", $this->defaultOptions);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Response code KO !');
            }
            return ($response->toArray())['results'];
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('getVideo error : %s', $e->getMessage()));
            return array_merge($this->defaultResponse, ['msg' => $e->getMessage()]);
        }
    }

}
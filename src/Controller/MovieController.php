<?php

namespace App\Controller;

use App\Service\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    /** @var Movie */
    private $movie;
    private $config;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
        $this->config = $this->movie->getConfiguration();
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function list(): Response
    {
        $firstMovie = $firstMovieVideo = null;
        $movies = $this->movie->getPopularMovies();
        if (!empty($movies)) {
            $firstMovie = array_shift($movies);
            $firstMovieVideo = $this->movie->getVideo($firstMovie['id']);
        }

        return $this->render('home.html.twig', [
            'config' => $this->config,
            'movies' => $this->movie->getPopularMovies(),
            'genres' => $this->movie->getGenres(),
            'firstMovie' => $firstMovie,
            'firstMovieVideo' => $firstMovieVideo
        ]);
    }

    /**
     * @Route("/view/{idMovie<\d+>}", name="movie_view")
     *
     */
    public function view(int $idMovie)
    {
        return $this->render('movie/view.html.twig', [
            'config' => $this->config,
            'movie' => $this->movie->getMovie($idMovie)
        ]);
    }

    /**
     * @Route("/search", name="movie_search")
     */
    public function search(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $text = $request->query->get('query');
        if(!empty($text)) {
            $response->setContent($this->movie->searchMovie($text));
        }

        return $response;
    }

    /**
     * @Route("/discover", name="movie_discover")
     */
    public function discover(Request $request)
    {
        $params = $request->query->all();

        if(!empty($params)) {
            return $this->render('movie/list.html.twig', [
                'config' => $this->config,
                'movies' => $this->movie->discoverMovie($params)
            ]);
        }

        return new Response();
    }

}
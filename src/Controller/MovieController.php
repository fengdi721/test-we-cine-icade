<?php

namespace App\Controller;

use App\Service\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('movie/list.html.twig', [
            'config' => $this->config,
            'movies' => $this->movie->getPopularMovies(),
            'genres' => $this->movie->getGenres()
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
}
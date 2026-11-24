<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CourseRepository $courseRepository,ActualiteRepository $actualiteRepository): Response
    {
        $courses = $courseRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            4
        );
        $actualites = $actualiteRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            4
        );
        return $this->render('home/index.html.twig',[
            'courses'=>$courses, 'actualites'=>$actualites,
                ]
        );
    }
}

<?php

namespace App\Controller\Admin;

use App\Repository\ActualiteRepository;
use App\Repository\CommentRepository;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(
        CourseRepository $courses,
        UserRepository $users,
        CommentRepository $comments,
        ActualiteRepository $news,
    ): Response {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'courses' => $courses->count([]),
                'publishedCourses' => $courses->count(['isPublished' => true]),
                'users' => $users->count(['isDisabled' => false]),
                'comments' => $comments->count(['isDesabled' => false]),
                'news' => $news->count([]),
            ],
        ]);
    }
}

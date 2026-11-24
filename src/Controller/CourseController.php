<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseCategoryRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourseController extends AbstractController
{
    #[Route('/courses', name: 'app_courses')]
    #[Route('/courses/categorie/{slug}', name: 'app_course_category')]
    public function courses(
        CourseRepository $courseRepository,
        CourseCategoryRepository $categoryRepository,
        ?string $slug = null
    ): Response {
        $currentCategory = null;

        if ($slug !== null) {
            $currentCategory = $categoryRepository->findOneBy(['slug' => $slug]);

            if (!$currentCategory) {
                throw $this->createNotFoundException('Catégorie introuvable.');
            }

            $courses = $courseRepository->findByCategorySlug($slug);
        } else {
            $courses = $courseRepository->findAll();
        }

        return $this->render('course/courses.html.twig', [
            'courses'         => $courses,
            'categories'      => $categoryRepository->findAll(),
            'currentCategory' => $currentCategory,
        ]);
    }
   #[Route('/courses/{slug}', name: 'app_course')]
public function course( CourseRepository $courseRepository,string $slug): Response{
            $course = $courseRepository->findOneBy(['slug' => $slug]);
        return $this->render('course/course.html.twig', ['course' => $course]);
   }
}

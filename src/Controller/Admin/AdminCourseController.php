<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/courses')]
#[IsGranted('ROLE_ADMIN')]
class AdminCourseController extends AbstractController
{
    #[Route('', name: 'admin_course_index')]
    public function index(Request $request, CourseRepository $repo, PaginatorInterface $paginator): Response
    {

        $query= $repo->findForpaginationFilter();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10
        );

        return $this->render('admin/course/courses.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/admin/course/new', name: 'admin_course_new')]
    #[Route('/admin/course/edit/{id}', name: 'admin_course_edit', requirements: ['id' => '\d+'])]
    public function form(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ?Course $course = null): Response {

        // Vérifier si c'est une création
        $isNew = $course === null;

        $course ??= new Course();

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($isNew) {
                $course->setCreatedAt(new \DateTimeImmutable());
                $course->setUser($this->getUser());
            }

            $course
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setSlug($slugger->slug($course->getName()));

            $em->persist($course);
            $em->flush();

            $this->addFlash('success', 'Le cours a bien été enregistré.');

            return $this->redirectToRoute('admin_course_index');
        }

        return $this->render('admin/course/form.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_course_toggle', methods: ['POST'])]
    public function toggle(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $course->getId(), $request->request->get('_token'))) {
            $course->setIsPublished(!$course->isPublished());
            $em->flush();
            $this->addFlash('success', $course->isPublished() ? 'Cours affiché.' : 'Cours masqué.');
        }

        return $this->redirectToRoute('admin_course_index');
    }

    #[Route('admin/course/delete/{id}', name: 'admin_course_delete')]
    public function delete(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $em->remove($course);
            $em->flush();
            $this->addFlash('success', 'Le cours a été supprimé.');
        }

        return $this->redirectToRoute('admin_course_index');
    }
}

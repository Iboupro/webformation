<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Course;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('course')]
final class CommentaireController extends AbstractController
{
    #[Route('/commentaires/{id}', name: 'app_commentaire')]
    public function index(Course $course ,CommentRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comments = $repository->findBy(
            ['courseId' => $course,'isDesabled' => false],
            ['createdAt' => 'DESC'],
        );
        return $this->render('course/course.html.twig', [
            'comments' => $comments,

        ]);
    }

    #[Route('/commentaires/form/{id}', name: 'app_commentaire_form')]
    public function form(Course $course,Request $request,EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $comment = new comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDesabled(false)
                    ->setUserId($this->getUser())
                    ->setCourseId($course);
            $manager->persist($comment);
            $manager->flush();

        }

        return $this->render('course/course.html.twig', [
            'form' => $form,

        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/news')]
#[IsGranted('ROLE_ADMIN')]
class AdminActualiteController extends AbstractController
{
    #[Route('', name: 'admin_news_index')]
    public function index(Request $request, ActualiteRepository $repo, PaginatorInterface $paginator): Response
    {
        $query = $repo->findForpaginationFilter();
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('admin/actualite/actualites.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'admin_news_new')]
    #[Route('/edit/{id}', name: 'admin_news_edit', requirements: ['id' => '\d+'])]
    public function form(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ?Actualite $news = null): Response
    {
        $isNew = $news==null ;
        $news ??= new Actualite();
        $form = $this->createForm(ActualiteType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             if ($isNew) {
                 $news
                     ->setCreatedAt(new \DateTimeImmutable())
                     ->setUser($this->getUser())
                     ->setSlug($slugger->slug($news->getName()));
             }

            $news->setUpdateAt(new \DateTimeImmutable());
            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'L\'actualité a bien été enregistrée.');

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/actualite/form.html.twig', ['news' => $news, 'form' => $form]);
    }

    #[Route('/{id}/delete', name: 'admin_news_delete', requirements: ['id' => '\d+'])]
    public function delete(Request $request, Actualite $news, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $news->getId(), $request->request->get('_token'))) {
            $em->remove($news);
            $em->flush();
            $this->addFlash('success', 'L\'actualité a été supprimée.');
        }

        return $this->redirectToRoute('admin_news_index');
    }
}

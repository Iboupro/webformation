<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('', name: 'admin_user_index')]
    public function index(Request $request, UserRepository $repo, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repo->createQueryBuilder('u')->orderBy('u.createdAt', 'DESC'),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/users.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'admin_user_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user,['with_role' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $user->setRoles([$form->get('role')->getData()])
                ->setIsDisabled(false)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setLastLogAt(new \DateTimeImmutable())
                ->setImage('default.png');
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été créé.');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/form.html.twig', ['form' => $form]);
    }

    /** Promouvoir un utilisateur au rôle administrateur. */
    #[Route('/{id}/promote', name: 'admin_user_promote', requirements: ['id' => '\d+'])]
    public function promote(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('promote' . $user->getId(), $request->request->get('_token'))) {
            $user->setRoles(['ROLE_ADMIN']);
            $em->flush();
            $this->addFlash('success', $user->getEmail() . ' est maintenant administrateur.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /** Afficher | masquer (désactiver) un utilisateur. */
    #[Route('/{id}/toggle', name: 'admin_user_toggle', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function toggle(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('warning', 'Vous ne pouvez pas désactiver votre propre compte.');

            return $this->redirectToRoute('admin_user_index');
        }

        if ($this->isCsrfTokenValid('toggle' . $user->getId(), $request->request->get('_token'))) {
            $user->setIsDisabled(!$user->isDisabled());
            $em->flush();
            $this->addFlash('success', $user->isDisabled() ? 'Utilisateur désactivé.' : 'Utilisateur réactivé.');
        }

        return $this->redirectToRoute('admin_user_index');
    }
}

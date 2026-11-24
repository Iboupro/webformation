<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserCompteType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/use')]
//Modification de compte
final class UseController extends AbstractController
{
    #[Route('/edit/compte/{id}', name: 'app_use_edit_compte')]
    public function index(User $user,Request $request,EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(UserCompteType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user ->setUpdatedAt(new \DateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('use/editCompte.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
    //Modification de mot de passe
    #[Route('/edit/password/{id}', name: 'app_use_edit_password')]
    public function editPassword(User $user,Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user ->setPassword($userPasswordHasher->hashPassword($user, $plainPassword))
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('use/editPassword.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}

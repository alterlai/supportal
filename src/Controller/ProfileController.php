<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordResetType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * Show user profile and handle password reset.
     * @Route("/profile", name="profile")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordResetType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $old_password = $data['old_password'];
            $new_password = $data['new_password'];
            $new_password_confirm = $data['new_password_confirm'];


            if (!$passwordEncoder->isPasswordValid($user, $old_password))
            {
                $this->addFlash("warning", "Wachtwoord komt niet overeen met het huidige wachtwoord.");
            }
            elseif ($old_password == $new_password)
            {
                $this->addFlash( "warning","Nieuw wachtwoord kan niet hetzelfde zijn als het oude wachtwoord.");
            }
            elseif ($new_password !== $new_password_confirm)
            {
                $this->addFlash("warning", "Nieuwe wachtwoorden komen niet overeen.");
            }
            else {
                $new_encoded_password = $passwordEncoder->encodePassword($user, $new_password);

                $user->setPassword($new_encoded_password);

                $entityManager->persist($user);

                $entityManager->flush();

                $this->addFlash("success", "Wachtwoord gewijzigd.");
            }
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}

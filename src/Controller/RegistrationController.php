<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\FactionRepository;
use App\Repository\UserRepository;
use App\Security\ThreeCrownsAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, ThreeCrownsAuthenticator $threeCrownsAuthenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser($user, $threeCrownsAuthenticator, $request);
            
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/register/finish/{id}/{isFirstCo}', name: 'presentation')]
    public function finishRegister(int $id, bool $isFirstCo, FactionRepository $factionRepository, Security $security) : Response
    {
        $user = $security->getUser();

        if (!$user || $isFirstCo == null || !$id) {
            // L'utilisateur n'est pas connecté, rediriger vers la page de connexion par exemple
            return $this->redirectToRoute('app_login', [
                'success' => true
            ]);
        }

        $houses = $factionRepository->findThreeHouses();

        return $this->render('registration/finish.html.twig', [
            'houses' => $houses,
            'userId' => $id
        ]);
    }

    #[Route('/register/finish/choose', name: 'choose_faction')]
    public function chooseFaction(Request $request, UserRepository $userRepository, Security $security)
    {
        $factionId = $request->query->get('factionId');
        $userId = $request->query->get('userId');
        $user = $security->getUser();

        if ($user->getId() == $userId) {
            if (!$factionId || !$userId) {
                throw new \InvalidArgumentException('You must provide factionId and userId.');
            }

            $userRepository->lockFaction($factionId, $userId);

            return $this->redirectToRoute('app_menu');

        } else {
            throw new \InvalidArgumentException('Error : Unauthorized');
        }


    }
}

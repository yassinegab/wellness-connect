<?php

namespace App\Controller\Front_office\Signup;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        if ($request->isMethod('POST')) {

            // ================= CSRF =================
            if (!$this->isCsrfTokenValid('register', $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_register');
            }

            // ================= MOT DE PASSE =================
            $plainPassword = $request->request->get('plainPassword');
            $confirmPassword = $request->request->get('confirm_password');

            if (empty($plainPassword)) {
                $this->addFlash('error', 'Mot de passe obligatoire.');
                return $this->redirectToRoute('app_register');
            }

            if ($plainPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_register');
            }

            // ================= EMAIL =================
            $email = trim($request->request->get('email', ''));
            if (empty($email)) {
                $this->addFlash('error', 'Email obligatoire.');
                return $this->redirectToRoute('app_register');
            }

            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // ================= CREATION USER =================
            $user = new User();
            $user->setNom(trim($request->request->get('nom', '')));
            $user->setPrenom(trim($request->request->get('prenom', '')));
            $user->setEmail($email);
            $user->setTelephone(trim($request->request->get('telephone', '')));

            // ================= ROLE =================
            $roleValue = $request->request->get('role');
            try {
                $user->setUserRole(UserRole::from($roleValue));
            } catch (\ValueError $e) {
                $user->setUserRole(UserRole::PATIENT); // rôle par défaut
            }

            // ================= INFOS PHYSIQUES =================
            // On ne demande ces infos que si ce n'est PAS un admin
            if ($user->getUserRole() !== UserRole::ADMIN) {
                $age = (int) $request->request->get('age', 0);
                $poids = (float) $request->request->get('poids', 0);
                $taille = (float) $request->request->get('taille', 0);
                $sexe = $request->request->get('sexe');

                // Validation stricte
                if ($age <= 0) {
                    $this->addFlash('error', 'Veuillez saisir un âge valide.');
                    return $this->redirectToRoute('app_register');
                }

                if ($poids <= 0 || $taille <= 0) {
                    $this->addFlash('error', 'Veuillez saisir un poids et une taille valides.');
                    return $this->redirectToRoute('app_register');
                }

                if (!in_array($sexe, ['Homme', 'Femme'])) {
                    $this->addFlash('error', 'Veuillez sélectionner un sexe valide.');
                    return $this->redirectToRoute('app_register');
                }

                $user->setAge($age);
                $user->setPoids($poids);
                $user->setTaille($taille);
                $user->setSexe($sexe);
            }

            // ================= HANDICAP =================
            $isHandicapped = $request->request->get('is_handicapped') ? true : false;
            $user->setHandicap($isHandicapped);

            // ================= PASSWORD HASH =================
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // ================= ENREGISTREMENT EN BASE =================
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire
        return $this->render('Front_office/registration/register.html.twig');
    }
}

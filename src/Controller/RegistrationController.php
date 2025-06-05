<?php

namespace App\Controller;

use App\Entity\User;
use Gumlet\ImageResize;
use Psr\Log\LoggerInterface;
use App\Form\RegistrationType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_registration')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        MailerInterface $mailer,
        LoggerInterface $logger // <- Add logger
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('plain_password')->getData());
            $user->setPassword($hashedPassword);

            // Gérer l'upload de l'image de profil
            $profileImage = $form->get('profile_image')->getData();

            if ($profileImage) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($profileImage->getMimeType(), $allowedMimeTypes)) {
                    $this->addFlash('error', 'Format d’image non supporté. Utilisez JPG, PNG ou WEBP.');
                    return $this->redirectToRoute('app_registration');
                }

                // Générer un nom unique
                $safeFilename = $slugger->slug(pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename . '-' . uniqid() . '.webp';

                // Redimensionner et enregistrer en WEBP
                try {
                    $image = new ImageResize($profileImage->getPathname());
                    $image->resizeToBestFit(200, 200);
                    $imagePath = $this->getParameter('kernel.project_dir') . '/public/assets/profileImage/' . $newFilename;
                    $image->save($imagePath, IMAGETYPE_WEBP);

                    // Assigner le fichier à l'utilisateur
                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                    return $this->redirectToRoute('app_registration');
                }
            }

            $verificationToken = Uuid::v4();
            $user->setVerificationToken($verificationToken);
            $user->setIsVerified(false);

            // Autres informations utilisateur
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer un email de confirmation
            try {
                $email = (new Email())
                    ->from('erwanncrevel.ec@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Confirm your email')
                    ->html("<p>Please confirm your email by clicking the link below:</p>
                        <p><a href='http://127.0.0.1:8000/verify-email/{$verificationToken}'>Confirm my account</a></p>");

                $mailer->send($email);
                $logger->info("Email sent to: " . $user->getEmail());
            } catch (\Exception $e) {
                $logger->error("Email failed: " . $e->getMessage());
            }

            // Rediriger vers la page de connexion
            $this->addFlash('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Rechercher l'utilisateur grâce au token
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_login');
        }

        // Mettre à jour l'utilisateur
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use Gumlet\ImageResize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {

        if ($this->getUser() !== null) {
            // get every models made by the user

            $user = $this->getUser();
            $models = $user->getModels();
            $models = $models->toArray();
            $modelsArray = [];
            foreach ($models as $model) {
                $modelsArray[] = [
                    'id' => $model->getId(),
                    'image' => $model->getImage(),
                    'createdAt' => $model->getCreatedAt()
                ];
            }
            $models = $modelsArray;

            // return every user information
            $userInfoRaw = $this->getUser();


            $userInfo = [
                'id' => $userInfoRaw->getId(),
                'email' => $userInfoRaw->getEmail(),
                'pseudo' => $userInfoRaw->getPseudo(),
                'name' => $userInfoRaw->getName(),
                'surname' => $userInfoRaw->getSurname(),
                'birthdate' => $userInfoRaw->getBirthdate(),
                'profileImage' => $userInfoRaw->getProfileImage(),
                'createdAt' => $userInfoRaw->getCreatedAt(),
                'models' => $models
            ];

            return $this->render('account/index.html.twig', [
                'controller_name' => 'AccountController',
                'userInfo' => $userInfo
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('/edit/{id}', name: 'app_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

        $jsonRequest = json_decode($request->getContent(), true);

        $id = $jsonRequest['id'];
        $email = $jsonRequest['email'];
        $pseudo = $jsonRequest['pseudo'];
        $name = $jsonRequest['name'];
        $surname = $jsonRequest['surname'];
        $birthdate = $jsonRequest['birthdate'];
        $birthdate = new \DateTime($birthdate);

        $user = $entityManagerInterface->getRepository(User::class)->find($id);

        if ($email !== null && $email !== $user->getEmail() && $email !== "") {
            $user->setEmail($email);
        }
        if ($pseudo !== null && $pseudo !== $user->getPseudo() && $pseudo !== "") {
            $user->setPseudo($pseudo);
        }
        if ($name !== null && $name !== $user->getName() && $name !== "") {
            $user->setName($name);
        }
        if ($surname !== null && $surname !== $user->getSurname() && $surname !== "") {
            $user->setSurname($surname);
        }
        if ($birthdate !== null && $birthdate !== $user->getBirthdate() && $birthdate !== "") {
            $user->setBirthdate($birthdate);
        }

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        return new JsonResponse([
            'message' => 'Informations modifiés avec succès',
            'request' => $jsonRequest
        ]);
    }

    #[Route('/editimage/{id}', name: 'app_editimage', methods: ['POST'])]
    public function editimage(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

        $file = $request->files->get('file');

        $jsonData = json_decode($request->getContent(), true);

        if ($file) {
            // get the user id from the request
            $id = $request->attributes->get('id');

            // get the user from the database
            $user = $entityManagerInterface->getRepository(User::class)->find($id);


            // Removing from the database and public folder the old image
            if ($user->getProfileImage() !== null) {
                unlink($this->getParameter('kernel.project_dir') . '/public/assets/profileImage/' . $user->getProfileImage());
            }

            $fileName = $file->getClientOriginalName();

            // Resize the image
            $image = new ImageResize($file->getPathname());
            $image->resize(200, 200); // Resize to a width of 800 pixels
            $webpFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';

            $image->save($this->getParameter('kernel.project_dir') . '/public/assets/profileImage/' . $webpFileName, IMAGETYPE_WEBP);

            $user->setProfileImage($webpFileName);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
        } else {
            return new JsonResponse([
                'message' => 'Aucune image détécté',
            ]);
        }


        return new JsonResponse([
            'message' => 'Photo de profil modifiés avec succès',
        ]);
    }
}

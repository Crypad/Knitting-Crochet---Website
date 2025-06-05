<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\InstagramPub;
use App\Entity\Publications;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Retrieve all the publications from the database where the type is card
        $publications = $entityManager->getRepository(Publications::class)->findByType('card');

        // Convert the publications into an array
        /* $publicationsArray = json_encode($publications); */

        /*         $propTest = [
            'card1' => [
                'id' => '1',
                'img' => 'https://picsum.photos/id/237/400/250',
                'title' => 'Ceci est un titre test',
                'desc' => 'Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés.Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés et surtout vive la soupe aux dentiers et la soupe aux dentiers et la soupe aux dentiers.'
            ],
            'card2' => [
                'id' => '2',
                'img' => 'https://picsum.photos/id/238/400/250',
                'title' => 'Ceci est un titre test',
                'desc' => 'Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés.Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés.'
            ],
            'card3' => [
                'id' => '3',
                'img' => 'https://picsum.photos/id/239/400/250',
                'title' => 'Ceci est un titre test',
                'desc' => 'Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés.Lorem ipsum dolor sit amet et mes couilles au bord de l\'eau, les canard viennent picorer les bout de pains que leurs lance les mémés particulivement agés.'
            ]
        ];

        /* $propTestArray = array_values($propTest); */

        return $this->render('home/index.html.twig', [
            'controller_name' => 'SYMFONY REACT TAILWIND FONTAWESOME',
            'publicationsArray' => $publications,
            /* 'propTest' => $propTest */
        ]);
    }

    // Conditions géneral d'utilisation
    #[Route('/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render('home/cgu.html.twig', [
            'controller_name' => 'SYMFONY REACT TAILWIND FONTAWESOME',
        ]);
    }


    #[Route('/fetch-publications', name: 'app_fetch_publications', methods: ['GET'])]
    public function fetchPublications(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $publications = $entityManager->getRepository(Publications::class)->findByType('card');

        $publicationsData = array_map(function ($pub) {
            return [
                'id'    => $pub->getId(),
                'content' => $pub->getContent(),
                'images'   => $pub->getImages(),
                'created_at' => $pub->getCreatedAt()
            ];
        }, $publications);


        return new JsonResponse($publicationsData);
    }

    // Route to get all the instagram publications from InstagramPub entity
    #[Route('/fetch-instagram-publications', name: 'app_fetch_instagram_publications', methods: ['GET'])]
    public function fetchInstagramPublications(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $instagramPublications = $entityManager->getRepository(InstagramPub::class)->findAll();

        $instagramPublicationsData = array_map(function ($pub) {
            // html encode the $pub->getInstagramPublications()
            $html = html_entity_decode($pub->getInstagramPublications());
            $scriptPattern = '/<script[^>]*>(.*?)<\/script>/s';
            $cleanedContent = preg_replace($scriptPattern, '', $html);
            return [
                'id'    => $pub->getId(),
                'content' => $cleanedContent
            ];
        }, $instagramPublications);


        return new JsonResponse($instagramPublicationsData);
    }

    #[Route('/fetch-annonce', name: 'app_fetch_annonce', methods: ['GET'])]
    public function fetchAnnonce(EntityManagerInterface $entityManager): JsonResponse
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findAll();
        if (!$annonces) {
            return new JsonResponse(['error' => 'Annonce not found'], 404);
        }

        $annonce = $annonces[0];

        return new JsonResponse($annonce->getContent());
    }
}

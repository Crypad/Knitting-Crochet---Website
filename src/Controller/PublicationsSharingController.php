<?php

namespace App\Controller;

use App\Entity\PublicationsSharing;
use App\Form\PublicationsSharingType;
use App\Repository\PublicationsSharingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/publications/sharing')]
final class PublicationsSharingController extends AbstractController{
    #[Route(name: 'app_publications_sharing_index', methods: ['GET'])]
    public function index(PublicationsSharingRepository $publicationsSharingRepository): Response
    {
        return $this->render('publications_sharing/index.html.twig', [
            'publications_sharings' => $publicationsSharingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_publications_sharing_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publicationsSharing = new PublicationsSharing();
        $form = $this->createForm(PublicationsSharingType::class, $publicationsSharing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($publicationsSharing);
            $entityManager->flush();

            return $this->redirectToRoute('app_publications_sharing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publications_sharing/new.html.twig', [
            'publications_sharing' => $publicationsSharing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_publications_sharing_show', methods: ['GET'])]
    public function show(PublicationsSharing $publicationsSharing): Response
    {
        return $this->render('publications_sharing/show.html.twig', [
            'publications_sharing' => $publicationsSharing,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_publications_sharing_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PublicationsSharing $publicationsSharing, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublicationsSharingType::class, $publicationsSharing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_publications_sharing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publications_sharing/edit.html.twig', [
            'publications_sharing' => $publicationsSharing,
            'form' => $form,
        ]);
    }

    // Post Route to delete a publication
    #[Route('/{id}', name: 'app_publications_sharing_delete', methods: ['POST'])]
    public function delete(Request $request, PublicationsSharing $publicationsSharing, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publicationsSharing->getId(), $request->request->get('_token'))) {
            $entityManager->remove($publicationsSharing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_publications_sharing_index', [], Response::HTTP_SEE_OTHER);
    }
}

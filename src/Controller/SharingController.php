<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Entity\User;
use App\Entity\Likes;
use App\Entity\Models;
use App\Entity\Comments;
use App\Entity\PublicationsSharing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SharingController extends AbstractController
{
    #[Route('/sharing', name: 'app_sharing')]
    public function index(EntityManagerInterface $em): Response
    {
        // Get all the publications
        $publicationsRaw = $em->getRepository(PublicationsSharing::class)->findAll();
        $publications = [];
        foreach ($publicationsRaw as $pub) {
            // Extract model data
            $models = [];
            foreach ($pub->getModels() as $model) {
                $models[] = [
                    'id' => $model->getId(),
                    'image' => $model->getImage(),  // Assuming you have an 'image' field in Models
                    'createdAt' => $model->getCreatedAt(), // If you want to include creation date
                ];
            }
            $tags = [];
            foreach ($pub->getTags() as $tag) {
                $tags[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getTagName(),
                ];
            }

            // Checks if this user has liked this publication using user_id and publication_id
            $hasLiked = $em->getRepository(Likes::class)->findOneBy([
                'user' => $this->getUser(),
                'publication' => $pub
            ]);
            $hasLiked = $hasLiked ? true : false;

            // Checks the amount of comments per publications and returns it
            $comments = $em->getRepository(Comments::class)->findBy([
                'publication' => $pub
            ]);
            $commentCount = count($comments);

            // Add to the publication array
            $publications[] = [
                'id' => $pub->getId(),
                'content' => $pub->getContent(),
                'images' => $pub->getImages(),
                'models' => $models,  // Now you're passing an array of model data
                'tags' => $tags,
                'likes' => $pub->getLikes(),
                'hasLiked' => $hasLiked,
                'commentCount' => $commentCount,
                'userPseudo' => $pub->getUser()->getPseudo(),
                'userAvatar' => $pub->getUser()->getProfileImage(),
                'created_at' => $pub->getCreatedAt(),
            ];
        }

        return $this->render('sharing/index.html.twig', [
            'controller_name' => 'SharingController',
            'publications' => $publications,
        ]);
    }
    #[Route('/sharing/add', name: 'app_add_pub', methods: ['POST'])]
    public function addPub(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Retrieve the uploaded file
        $imageFile = $request->files->get('image');
        if (!$imageFile) {
            return new JsonResponse(["error" => "No image uploaded"], Response::HTTP_BAD_REQUEST);
        }

        // Move the uploaded file to the desired directory
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/assets/sharing/';
        $newFilename = uniqid() . '.' . $imageFile->guessExtension();
        $imageFile->move($uploadsDir, $newFilename);

        // Retrieve other form fields
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        $models = json_decode($request->request->get('models'), true) ?? [];
        $tags = json_decode($request->request->get('tags'), true) ?? [];

        // Ensure tags is an array
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        // Ensure content is stored correctly
        $contentArray = [
            'title' => $title,
            'content' => strip_tags($content)
        ];

        $pub = new PublicationsSharing();
        $pub->setContent($contentArray);
        $pub->setLikes(0);
        $pub->setImages(is_array($newFilename) ? $newFilename : [$newFilename]); // Convert string to array

        foreach ($models as $modelName) {
            $modelEntity = $em->getRepository(\App\Entity\Models::class)->findOneBy(['image' => $modelName]);
            if ($modelEntity) {
                $pub->addModel($modelEntity);
            }
        }
        /* $pub->addTag($tags); */

        foreach ($tags as $tagName) {
            if (trim($tagName) !== "") {
                $tagEntity = $em->getRepository(\App\Entity\Tags::class)
                    ->findOneBy(['tag_name' => $tagName]);
                if (!$tagEntity) {
                    // Optionnel : créer le tag s'il n'existe pas
                    $tagEntity = new \App\Entity\Tags();
                    $tagEntity->setTagName($tagName);
                    $em->persist($tagEntity);
                }
                $pub->addTag($tagEntity);
            }
        }

        $pub->setUser($this->getUser());
        $pub->setCreatedAt(new \DateTimeImmutable());

        $em->persist($pub);
        $em->flush();

        return new JsonResponse(["msg" => "Publication added successfully"]);
    }

    #[Route('/sharing/getmodels', name: 'app_get_models')]
    public function getModels(Security $security): JsonResponse
    {
        // Get the currently logged-in user
        $user = $security->getUser();

        // Ensure the user is authenticated
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // If user has no models, return an empty array
        if ($user->getModels()->isEmpty()) {
            return new JsonResponse([]);
        }

        // Convert models to an array
        $modelsArray = [];
        foreach ($user->getModels() as $model) {
            $modelsArray[] = [
                'id' => $model->getId(),
                'image' => $model->getImage(),
                'createdAt' => $model->getCreatedAt()
            ];
        }

        return new JsonResponse($modelsArray);
    }

    // POST ROUTE to get tags by request
    #[Route('/sharing/searchtag', name: 'app_search_tag', methods: ['POST'])]
    public function getTags(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $prefix = $data['prefix'] ?? '';

        $tags = $em->getRepository(Tags::class)->findByPrefix($prefix);

        return new JsonResponse(["tagsList" => $tags]);
    }

    // POST ROUTE to get Publications by tag
    #[Route('/sharing/getpublicationsbytag', name: 'app_get_publications_by_tag', methods: ['POST'])]
    public function getPublicationsByTag(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $tagName = $data['tag'] ?? '';

        if (empty($tagName)) {
            return new JsonResponse(['error' => 'No tag provided'], Response::HTTP_BAD_REQUEST);
        }

        $publicationsRaw = $em->getRepository(PublicationsSharing::class)->findByApproximateTagName($tagName);

        $publications = [];

        foreach ($publicationsRaw as $pub) {
            // Extract model data
            $models = [];
            foreach ($pub->getModels() as $model) {
                $models[] = [
                    'id' => $model->getId(),
                    'image' => $model->getImage(),  // Assuming you have an 'image' field in Models
                    'createdAt' => $model->getCreatedAt(), // If you want to include creation date
                ];
            }
            $tags = [];
            foreach ($pub->getTags() as $tag) {
                $tags[] = [
                    'id' => $tag->getId(),
                    'name' => $tag->getTagName(),
                ];
            }

            // Checks if this user has liked this publication using user_id and publication_id
            $hasLiked = $em->getRepository(Likes::class)->findOneBy([
                'user' => $this->getUser(),
                'publication' => $pub
            ]);
            $hasLiked = $hasLiked ? true : false;

            // Add to the publication array
            $publications[] = [
                'id' => $pub->getId(),
                'content' => $pub->getContent(),
                'images' => $pub->getImages(),
                'models' => $models,  // Now you're passing an array of model data
                'tags' => $tags,
                'likes' => $pub->getLikes(),
                'hasLiked' => $hasLiked,
                'userPseudo' => $pub->getUser()->getPseudo(),
                'userAvatar' => $pub->getUser()->getProfileImage(),
                'created_at' => $pub->getCreatedAt(),
            ];
        }

        return new JsonResponse(["publications" => $publications]);
    }
    
    // Route to add a like to a publication
    #[Route('/sharing/addlike', name: 'app_add_like', methods: ['POST'])]
    public function addLike(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Get the publication ID from the request
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        // Find the publication by ID
        $publication = $em->getRepository(PublicationsSharing::class)->find($id);
        if (!$publication) {
            return new JsonResponse(['error' => 'Publication not found'], Response::HTTP_NOT_FOUND);
        }

        $like = new Likes();
        $like->setUser($this->getUser());
        $like->setPublication($publication);

        $em->persist($like);
        $em->flush();

        // Mettre a jour le nombre de likes
        $publication->setLikes($publication->getLikes() + 1);
        $em->persist($publication);
        $em->flush();

        return new JsonResponse(['message' => 'Like added successfully']);
    }

    // Route to remove a like from a publication
    #[Route('/sharing/removelike', name: 'app_remove_like', methods: ['POST'])]
    public function removeLike(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Get the publication ID from the request
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        // Find the publication by ID
        $publication = $em->getRepository(PublicationsSharing::class)->find($id);
        if (!$publication) {
            return new JsonResponse(['error' => 'Publication not found'], Response::HTTP_NOT_FOUND);
        }

        $like = $em->getRepository(Likes::class)->findOneBy([
            'user' => $this->getUser(),
            'publication' => $publication
        ]);

        if (!$like) {
            return new JsonResponse(['error' => 'Like not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($like);
        $em->flush();

        // Mettre a jour le nombre de likes
        $publication->setLikes($publication->getLikes() - 1);
        $em->persist($publication);
        $em->flush();

        return new JsonResponse(['message' => 'Like removed successfully']);
    }

    // Route to add a comment to a publication
    #[Route('/sharing/addcomment', name: 'app_add_comment', methods: ['POST'])]
    public function addComment(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Get the publication ID and comment from the request
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $content = $data['comment'];
        $isAnswer = $data['isAnswer'];
        $answerId = $data['answerId'];


        // Find the publication by ID
        $publication = $em->getRepository(PublicationsSharing::class)->find($id);
        if (!$publication) {
            return new JsonResponse(['error' => 'Publication not found'], Response::HTTP_NOT_FOUND);
        }

        $comment = new Comments();
        $comment->setUser($this->getUser());
        $comment->setPublication($publication);
        $comment->setContent($content);
        $comment->setIsAnAnswer($isAnswer);
        $comment->setAnswerUserId($answerId);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $em->persist($comment);
        $em->flush();

        return new JsonResponse(['message' => 'Comment added successfully']);
    }

    // Route to get all comments for a publication
    #[Route('/sharing/getcomments', name: 'app_get_comments', methods: ['POST'])]
    public function getComments(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Get the publication ID from the request
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        // Find the publication by ID
        $publication = $em->getRepository(PublicationsSharing::class)->find($id);
        if (!$publication) {
            return new JsonResponse(['error' => 'Publication not found'], Response::HTTP_NOT_FOUND);
        }

        $commentsRaw = $em->getRepository(Comments::class)->findBy(['publication' => $publication]);

        $comments = [];

        foreach ($commentsRaw as $comment) {
            if ($comment->getUser() === null) {
                // Remove the comment from the database
                $em->remove($comment);
                continue; // Skip this comment and don't add it to the response
            }

            $comments[] = [
                'id' => $comment->getId(),
                'user' => $comment->getUser()->getPseudo(),
                'userId' => $comment->getUser()->getId(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d'),
                'isAnswer' => $comment->isAnAnswer(),
                'userAvatar' => $comment->getUser()->getProfileImage(),
                'answerUserID' => $comment->getAnswerUserId(),
                'publicationID' => $comment->getPublication()->getId(),
            ];
        }

        // Persist the removal of orphaned comments
        $em->flush();

        return new JsonResponse(['comments' => $comments]);
    }

    #[Route('/sharing/getuserid', name: 'app_get_user_id', methods: ['GET'])]
    public function getUserID(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['userPseudo' => $user->getPseudo()]);
    }

    #[Route('/sharing/deletePublication', name: 'app_publications_sharing_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true); // Decode JSON body
        $pubId = $data['id'] ?? null; // Retrieve the 'id' field

        if (!$pubId) {
            return new JsonResponse(['error' => 'Missing publication ID'], Response::HTTP_BAD_REQUEST);
        }

        $publication = $em->getRepository(PublicationsSharing::class)->find($pubId);

        if (!$publication) {
            return new JsonResponse(['error' => 'Publication not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($publication);
        $em->flush();

        return new JsonResponse(['message' => 'Publication deleted successfully']);
    }
}

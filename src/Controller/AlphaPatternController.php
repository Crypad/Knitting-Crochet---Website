<?php

namespace App\Controller;

use App\Entity\Models;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AlphaPatternController extends AbstractController
{
    #[Route('/alphapattern', name: 'app_alpha_pattern')]
    public function index(): Response
    {
        return $this->render('alpha_pattern/index.html.twig', [
            'controller_name' => 'AlphaPatternController',
        ]);
    }

    #[Route('/alphapattern/upload', name: 'app_alpha_pattern_upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['image'])) {
            return new JsonResponse(["msg" => "No image received"], 400);
        }

        $base64 = $data['image'];

        // Remove "data:image/png;base64," from the Base64 string
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $binaryData = base64_decode($base64);

        if (!$binaryData) {
            return new JsonResponse(["msg" => "Invalid Base64"], 400);
        }

        // Generate a unique filename
        $fileName = $data['fileName'] . '.png';
        $filePath = $this->getParameter('kernel.project_dir') . '/public/assets/models/' . $fileName;

        // Save the file
        file_put_contents($filePath, $binaryData);

        // Save in database (assuming you have an `image` field)
        $alphaPattern = new Models();
        $alphaPattern->setImage($fileName);
        $alphaPattern->setUser($this->getUser());
        $alphaPattern->setCreatedAt(new \DateTimeImmutable());
        $em->persist($alphaPattern);
        $em->flush();

        return new JsonResponse([
            "msg" => "success",
            "filePath" => "/assets/alphaPattern/" . $fileName
        ]);
    }
    
    // Route to delete a model via its ID
    #[Route('/alphapattern/delete', name: 'app_alpha_pattern_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {   
        // Get the model ID from the request
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        if (!$id) {
            return new JsonResponse(["msg" => "Model ID not provided"], 400);
        }

        $alphaPattern = $em->getRepository(Models::class)->find($id);

        if (!$alphaPattern) {
            return new JsonResponse(["msg" => "Model not found"], 404);
        }

        $em->remove($alphaPattern);
        $em->flush();

        return new JsonResponse(["msg" => "Model deleted successfully"]);
    }
}

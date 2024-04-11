<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectApiController extends AbstractController
{

    #[Route('/projectApi', name: 'app_project_api')]
    public function index(): Response
    {
    
        return $this->render('project_api/index.html.twig', [
            'controller_name' => 'ProjectBackController',
        ]);
    }

    #[Route('/projectApi/back/api/projects', name: 'app_project_back_api_projects')]
    public function projects(): JsonResponse
    {
        $client = HttpClient::create();
        try {
            $response = $client->request('GET', 'http://192.168.62.3/api/projects');
            $content = $response->getContent();
            $data = json_decode($content, true);
            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/projectApi/back/api/projects/delete/{id}', name: 'app_project_back_api_projects_delete')]
    public function deleteProject($id): JsonResponse
    {
        $client = HttpClient::create();
        $url = 'http://192.168.62.3/api/projects/'.$id;

        try {

            $response = $client->request('DELETE', $url);
            return new JsonResponse(['success' => true]);
        } catch (TransportExceptionInterface $e) {
            // Handle transport exception
            return new JsonResponse(['error' => 'Failed to delete item'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/projectApi/back/api/projects/create', name: 'app_project_back_api_projects_create')]
    public function createProject(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $baseUrl = 'http://192.168.62.3/api/projects';
      
        $client = HttpClient::create();
        try {
            $response = $client->request('POST', $baseUrl, [
                'json' => $requestData,
            ]);


            $data = $response->toArray();
            return new JsonResponse($data, JsonResponse::HTTP_CREATED);
        } catch (TransportExceptionInterface $e) {
            // Handle transport exception
            return new JsonResponse(['error' => 'Failed to create item'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

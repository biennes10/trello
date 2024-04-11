<?php

namespace App\Controller;

use App\Form\ProjectType;
use App\Entity\Project;
use App\Entity\Categorie;
use App\Entity\Ticket;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_home_empty')]
    #[Route('/home', name: 'app_home')]
    public function index(Security $security): Response
    {
        
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'zzz',
            ]);
        }else{
            return $this->redirectToRoute("app_login");
        }
       
    }

    #[Route('/project/new', name: 'app_new_project', methods: ['POST','GET'])]
    public function createProject(Request $request): Response
    {
    
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('home/newProject.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function project(Project $project): Response
    {
        
        return $this->render('home/project.html.twig', [
            'project' => $project,
        ]);
    }


    #[Route('/project/api/{id}', name: 'project_data')]
    public function projectData($id): Response
    {
        $entityManager = $this->entityManager;
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        $projectData = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'categories' => [],
        ];

        foreach ($project->getCategories() as $category) {
            $categoryData = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'tickets' => [],
            ];

            foreach ($category->getTickets() as $ticket) {
                $ticketData = [
                    'id' => $ticket->getId(),
                    'name' => $ticket->getName(),
                    'description' => $ticket->getDescription()
                ];
                $categoryData['tickets'][] = $ticketData;
            }

            $projectData['categories'][] = $categoryData;
        }

        return new JsonResponse($projectData);
    }

    #[Route('/project/api/{projectId}/categories', name: 'api_category_create', methods: ['POST'])]
    public function createCategory(Request $request, $projectId): JsonResponse
    {
        
        $entityManager = $this->entityManager;
        $project = $entityManager->getRepository(Project::class)->find($projectId);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], 404);
        }

        // Récupérer les données envoyées dans le corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si le nom de la catégorie est présent dans les données
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Category name is required'], 400);
        }

        // Créer une nouvelle catégorie
        $category = new Categorie();
        $category->setName($data['name']);
        $category->setProject($project);

        // Enregistrer la nouvelle catégorie dans la base de données
        $entityManager->persist($category);
        $entityManager->flush();

        // Retourner une réponse JSON avec les données de la nouvelle catégorie
        return new JsonResponse(['category' => $category], 201);
    }

    #[Route('/project/api/{projectId}/categories/{categoryId}/tickets', name: 'api_ticket_create', methods: ['POST'])]
    public function createTicket(Request $request, $projectId, $categoryId): JsonResponse
    {
 
        $entityManager = $this->entityManager;
        $category = $entityManager->getRepository(Categorie::class)->find($categoryId);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], 404);
        }

        // Récupérer les données envoyées dans le corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si le titre du ticket est présent dans les données
        if (!isset($data['name']) || !isset($data['description'])) {
            return new JsonResponse(['error' => 'Ticket title and description are required'], 400);
        }
        
        // Créer une nouvelle instance de l'entité Ticket
        $ticket = new Ticket();
        $ticket->setName($data['name']);
        $ticket->setDescription($data['description']);
        $ticket->setCategorie($category);
        
        // Enregistrer le nouveau ticket dans la base de données
        $entityManager->persist($ticket);
        $entityManager->flush();

        // Retourner une réponse JSON avec les données du nouveau ticket
        return new JsonResponse(['ticket' => $ticket], 201);
    }

    #[Route('/project/api/{projectId}/categories/{categoryId}/tickets/{ticketId}', name: 'api_ticket_delete', methods: ['DELETE'])]
    public function delete($projectId, $categoryId, $ticketId): JsonResponse
    {
        $entityManager = $this->entityManager;
        $ticket = $entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            return new JsonResponse(['error' => 'Ticket not found'], 404);
        }
        
        // Vérifier que le ticket appartient à la catégorie spécifiée
        if ($ticket->getCategorie()->getId() != $categoryId) {
            return new JsonResponse(['error' => 'Ticket does not belong to the specified category'], 400);
        }

        // Supprimer le ticket de la base de données
        $entityManager->remove($ticket);
        $entityManager->flush();

        // Retourner une réponse JSON pour indiquer que le ticket a été supprimé avec succès
        return new JsonResponse(null, 204);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

// Use pour le jsonLogin
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Core\User\InMemoryUser;

class DefaultController extends AbstractController
{

   #[Route('/api/login', name: 'api_login', methods: ['POST'])]
   public function jsonlogin(#[CurrentUser] ?InMemoryUser $user): Response
   {

         if (null === $user) {
             return $this->json([
                 'message' => 'missing credentials',
             ], Response::HTTP_UNAUTHORIZED);
         }
	$token=uniqid();
	return new JsonResponse(['token' => $token], 200);
   }

    #[Route('/default', name: 'app_default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
	    'username' => 'Fabrice',
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'username' => 'Fabrice',
        ]);
    }

    #[Route('/api/helloworld/{name}', name: 'api_helloworld')]
    public function apiHelloWorld(string $name): Response
    {
        return new JsonResponse('hello '. $name);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {

	$error = $authenticationUtils->getLastAuthenticationError();
	$lastUsername = $authenticationUtils->getLastUsername();
	return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error ]);
    }

   #[Route('/logout', name: 'app_logout')]
   public function logout() {
	throw new \LogicException('Utilisateur déconnecté');
   }

}


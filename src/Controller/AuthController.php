<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(SessionInterface $session)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Test');
        $client->setClientId($_ENV[('GOOGLE_CLIENT_ID')]);
        $client->setClientSecret($_ENV[('GOOGLE_CLIENT_SECRET')]);
        $client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/auth-callback');
        $client->addScope([\Google_Service_Oauth2::USERINFO_PROFILE, \Google_Service_Oauth2::USERINFO_EMAIL]);
        $authUrl = $client->createAuthUrl();

        return $this->render('auth/index.html.twig', [
            'auth_url' => $authUrl,
        ]);
    }

    /**
     * @Route("/auth-callback", name="auth_callback")
     */
    public function callback(SessionInterface $session)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Test');
        $client->setClientId($_ENV[('GOOGLE_CLIENT_ID')]);
        $client->setClientSecret($_ENV[('GOOGLE_CLIENT_SECRET')]);
        $client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/auth-callback');

        if (!isset($_GET['code'])) {
            $this->redirectToRoute('auth');
        }

        $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $accessToken = $client->getAccessToken();

        $oauth2 = new \Google_Service_Oauth2($client);
        dd($oauth2->userinfo->get());

    }
}

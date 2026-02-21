<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $route = 'admin_dashboard';
        } elseif (in_array('ROLE_MEDECIN', $roles, true)) {
            // ✅ Changé de medecin_dossier_consulter à medecin_dashboard
            $route = 'medecin_dashboard';
        } else {
            $route = 'frontoffice_dashboard';
        }

        return new RedirectResponse($this->urlGenerator->generate($route));
    }
}
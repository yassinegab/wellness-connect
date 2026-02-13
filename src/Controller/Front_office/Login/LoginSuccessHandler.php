<?php

namespace App\Controller\Front_office\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use App\Enum\UserRole;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token
    ): RedirectResponse {
        $user = $token->getUser();

        switch ($user->getUserRole()) {
            case UserRole::ADMIN:
                return new RedirectResponse(
                    $this->router->generate('admin_dashboard')
                );

            case UserRole::MEDECIN:
                return new RedirectResponse(
                    $this->router->generate('medecin_dashboard')
                );

            case UserRole::PATIENT:
                return new RedirectResponse(
                    $this->router->generate('patient_dashboard')
                );
        }

        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}

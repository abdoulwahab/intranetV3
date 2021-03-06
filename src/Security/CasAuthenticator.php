<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Security/CasAuthenticator.php
// @author     David Annebicque
// @project intranetv3
// @date 28/11/2019 15:38
// @lastUpdate 28/11/2019 15:38

namespace App\Security;

use App\Entity\Departement;
use App\Event\CASAuthenticationFailureEvent;
use App\Events;
use App\Repository\DepartementRepository;
use phpCAS;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CasAuthenticator extends AbstractGuardAuthenticator
{
    private $urlGenerator;
    private $user;
    private $session;

    /** @var DepartementRepository */
    private $departementRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        DepartementRepository $departementRepository,
        SessionInterface $session

    ) {
        $this->urlGenerator = $urlGenerator;
        $this->departementRepository = $departementRepository;
        $this->session = $session;
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() === '/sso/cas';
    }

    public function getCredentials(Request $request)
    {
        $cas_host = 'cas.univ-reims.fr';
        // Context of the CAS Server
        $cas_context = '/cas';
        // Port of your CAS server. Normally for a https server it's 443
        $cas_port = 443;

        phpCAS::setDebug();
        phpCAS::setVerbose(true);
        phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
        phpCAS::setFixedServiceURL($this->urlGenerator->generate('cas_return', [],
            UrlGeneratorInterface::ABSOLUTE_URL));
//        phpCAS::setFixedServiceURL($this->urlGenerator->generate('default_homepage', [],
//            UrlGeneratorInterface::ABSOLUTE_URL));

        phpCAS::setNoCasServerValidation();
        phpCAS::forceAuthentication();

        if (phpCAS::getUser()) {
            return phpCAS::getUser();
        }

        return null;

    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $this->user = $userProvider->loadUserByUsername($credentials);

        return $this->user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        $def_response = new JsonResponse($data, 403);
        $event = new CASAuthenticationFailureEvent($request, $exception, $def_response);

        return $event->getResponse();

    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $rolesTab = $token->getRoleNames();
        if (in_array('ROLE_SUPER_ADMIN', $rolesTab, true) || in_array('ROLE_ADMINISTRATIF', $rolesTab, true)) {
            // c'est un super administrateur : on le rediriger vers l'espace super-admin
            $redirection = new RedirectResponse($this->urlGenerator->generate('super_admin_homepage'));
        } elseif (in_array('ROLE_PERMANENT', $rolesTab, true) || in_array('ROLE_ETUDIANT', $rolesTab, true)) {
            // c'est un utilisaeur étudiant ou prof : on le rediriger vers l'accueil

            if (in_array('ROLE_PERMANENT', $rolesTab, true)) {
                //init de la session departement
                $departements = $this->departementRepository->findDepartementPersonnelDefaut($this->user);
                if (count($departements) > 1) {
                    return new RedirectResponse($this->urlGenerator->generate('security_choix_departement'));
                }

                if (count($departements) === 1) {
                    /** @var Departement $departement */
                    $departement = $departements[0];
                    $this->session->set('departement', $departement->getUuidString()); //on sauvegarde
                } else {
                    echo 'pas de departement par defaut';
                    //pas de departement par défaut, ou pas de departement du tout.
                    $departements = $this->departementRepository->findDepartementPersonnel($this->user);
                    if (count($departements) === 0) {
                        return new RedirectResponse($this->urlGenerator->generate('security_login',
                            ['events' => Events::REDIRECT_TO_LOGIN, 'message' => 'pas-departement']));
                    }
                    //donc il y a une departement, mais pas une par défaut.
                    return new RedirectResponse($this->urlGenerator->generate('security_choix_departement'));
                }
            }
            $redirection = new RedirectResponse($this->urlGenerator->generate('default_homepage'));
        } else {
            //c'est aucun des rôles...
            $redirection = new RedirectResponse($this->urlGenerator->generate('security_login',
                ['message' => 'erreur_role']));
        }

        return $redirection;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {}
}

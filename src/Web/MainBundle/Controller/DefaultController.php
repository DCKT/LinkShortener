<?php

namespace Web\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Web\MainBundle\Entity\Link;
use Symfony\Component\HttpFoundation\Response;
use Web\MainBundle\Controller\Date;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
	// Page d'accueil
    public function indexAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();

        // Si l'utilisateur n'est pas connecté, on le redirige
         if (!is_object($user)):
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        endif;

        $request = $this->get('request');
        $referer = $request->headers->get('referer');  
        
        var_dump($referer);
    	// Création du formulaire
    	$link = new Link();
        $form = $this->createFormBuilder($link)
                        ->add('name', 'text')
                        ->add('OriginalURL', 'text')
                        ->getForm();

        // Récupère les liens de l'utilisateur connecté
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $link_list = $repo->getUserLink($user);
    
        /*        
        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        */
        return $this->render('WebMainBundle:Default:index.html.twig', array(
        	'form' => $form->createView(),
        	'link_list' => $link_list
        ));
    }


    // Méthode permettant d'ajouter un lien à la DB
    public function addLinkAction()
    {
    	// On récupère l'utilisateur
	   	$user = $this->container->get('security.context')->getToken()->getUser();

	   	// On vérifie le type de requête
	   	$request = $this->get('request');
	   	if( $request->getMethod() == 'POST' )
        {
            $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');

            // On vérifie si les infos envoyées ne sont pas vides
            if (!isset($_POST['form']['name']) || !isset($_POST['form']['OriginalURL']) ) {
                return $this->redirect($this->generateUrl('index'));
            }

        	$link = new Link();
        	// On récupère les informations du formulaire
            $name = $_POST['form']['name'];
            $originURL = $_POST['form']['OriginalURL'];
            $date = new \DateTime();
            $dateClicked = new \DateTime();
            // Algo génération url raccourcie
            $i = 0;
            $letter = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
            $shortUrl = array();
            $urlSet = false;

            do {
                if (count($shortUrl) < 5):
                    $nb = rand(0,1);
                    // Si nb = 0, on ajoute un chiffre
                    if ($nb == 0):
                        $shortUrl[$i] = rand(0,9);
                    else: // Sinon on rajoute une lettre
                        $randLetter = rand(0, 25);
                        $shortUrl[$i] = $letter[$randLetter];
                    endif;
                    $i++;
                else :
                    if (implode($shortUrl) != $repo->findBy(array('shortenedURL' => implode($shortUrl))) ):
                        $urlSet = true;
                    endif;
                endif;
            } while ($urlSet == false);

            // On assigne à l'entité
            $link->setName($name);
            $link->setOriginalURL($originURL);
            $link->setShortenedURL(implode($shortUrl));
            $link->setClicks(0);
            $link->setDateCreated($date);
            $link->addUser($user);
            $link->setEnabled(1);
            $link->setTimeLastClicked($dateClicked);
            // On ajoute à la DB
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($link);
            $manager->flush();

            return $this->redirect($this->generateUrl('index'));
        }
        // Gestion des erreurs
        else { 
            return $this->redirect($this->generateUrl('index', array('error' => 'true')));
        }
    }

    //Méthode permettant d'obtenir plus d'info sur un lien précis
    public function infoLinkAction($link) 
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Si l'utilisateur n'est pas connecté, on le redirige
         if (!is_object($user)):
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        endif;
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $infoLink = $repo->findOneBy(array('name' => $link ));
        return $this->render('WebMainBundle:Default:info.html.twig', array(
            'link' => $infoLink,
        ));
    }


    // Méthode permettant de désactiver un lien
    public function disabledAction($link)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $infoLink = $repo->findOneBy(array('shortenedURL' => $link ));

        if ($infoLink->getEnabled() == 1) 
            $infoLink->setEnabled(0);
        else 
            $infoLink->setEnabled(1);

        $em->persist($infoLink);
        $em->flush();
        return new Response("Link update");
    }


    // Méthode de suppression de lien
    public function removeAction($link)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        // Si l'utilisateur n'est pas connecté, on le redirige
        if (!is_object($user)):
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        endif;

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $infoLink = $repo->findOneBy(array('shortenedURL' => $link ));
        $em->remove($infoLink);
        $em->flush();

        return new Response('Link delete');
    }

    // Méthode permettant de rediriger les short URL
    public function redirectToAction($link)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $infoLink = $repo->findOneBy(array('shortenedURL' => $link ));
        
        /* 
            Gestion du conflit avec la route /login 

            On vérifie si infoLink n'est pas un objet
        */
        if (!is_object($infoLink)) {
            $request = $this->container->get('request');
            /* @var $request \Symfony\Component\HttpFoundation\Request */
            $session = $request->getSession();
            /* @var $session \Symfony\Component\HttpFoundation\Session */

            // get the error if any (works with forward and redirect -- see below)
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            } else {
                $error = '';
            }

            if ($error) {
                // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
                $error = $error->getMessage();
            }
            // last username entered by the user
            $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

            $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

            return $this->renderLogin(array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token' => $csrfToken,
            ));
        }

        else {
            $infoLink->setClicks($infoLink->getClicks() + 1);
            $lastClick = new \DateTime();
            $info->setTimeLastClicked($lastClick);
            $em->persist($infoLink);
            $em->flush();
            // Si le lien est toujours actif
            if ($infoLink->getEnabled() == 1) {
                return $this->redirect($infoLink->getOriginalURL());
            }
            else {
                return new Response("Ce lien est désactivé.");
            } 
        }
    }
    protected function renderLogin(array $data)
    {
        $template = sprintf('FOSUserBundle:Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
<?php

namespace Web\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
// On utilise nos entités
use Web\MainBundle\Entity\Link;
use Web\MainBundle\Entity\Referer;
use Web\MainBundle\Entity\Country;
use Web\MainBundle\Entity\DateClick;


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

        
    	// Création du formulaire
    	$link = new Link();
        $form = $this->createFormBuilder($link)
                        ->add('name', 'text')
                        ->add('OriginalURL', 'text')
                        ->getForm();

        // Récupère les liens de l'utilisateur connecté
        $repo = $this->getDoctrine()->getManager()->getRepository('WebMainBundle:Link');
        $link_list = $repo->getUserLink($user);
    
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
            $link->setClicksDay(0);
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
        $infoLink = $repo->findOneBy(array('shortenedURL' => $link ));
        // Récupère les sites référants et les pays
        $referer = $infoLink->getReferer()->toArray();
        $country = $infoLink->getCountry()->toArray();
        $clicks = $infoLink->getDateClick()->toArray();

        // On calcule le nombre de lien direct en fonction des réferants
        $direct = 0;
        foreach ($referer as $ref) {
            $direct += $ref->getTotal();            
        }

        $direct = $infoLink->getClicks() - $direct;
        
        // On vérifie si le lien existe
        if (!is_object($infoLink)) {
            return new Response("Lien  inexistant");
        }

        return $this->render('WebMainBundle:Default:info.html.twig', array(
            'link' => $infoLink,
            'referer' => $referer,
            'country' => $country,
            'direct' => $direct,
            'clicks' => $clicks
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
        
        $request = $this->container->get('request');

        /* 
            Gestion du conflit avec la route /login 

            On vérifie si infoLink n'est pas un objet
        */
        if (!is_object($infoLink)) {
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

        // On redirige l'user vers le lien
        else {
            

            if ($infoLink->getEnabled() != 1):
                return new Response("Ce lien est désactivé.");
            endif;
            $referer = $request->headers->get('referer');
            // On récupère l'ip de l'user pour déterminer son pays
            $ip = $this->get('request')->server->get('REMOTE_ADDR');

            if (!empty($ip)) {
               $country = file_get_contents('http://api.hostip.info/country.php?ip='.$ip);
            }

            // On assigne le pays d'ou l'utilisateur vient
            if (isset($country)) {
                $cnList = $infoLink->getCountry()->toArray();
                $new = true;

                foreach ($cnList as $c) {
                    if ($c->getName() == $country) {
                        $c->setTotal($c->getTotal() + 1);
                        $new = false;
                    }
                }
                if ($new == true) {
                    $cou = new Country();
                    $cou->setName($country);
                    $cou->setTotal(1);
                    $infoLink->addCountry($cou);
                }
            }
            
            // On assigne le site d'ou l'utilisateur vient
            if ($referer != NULL):
                $refList = $infoLink->getReferer()->toArray();
                $check = true;
                // On vérifie si le site existe déjà et on l'incrémente
                foreach ($refList as $r) {
                    if ($r->getWebsiteUrl() == $referer):
                        $r->setTotal($r->getTotal() + 1);
                        $check = false;
                        break;
                    endif;
                }
                // Sinon on l'ajoute à la base
                if ($check == true):
                    $ref = new Referer();
                    $ref->setWebsiteUrl($referer);
                    $ref->setTotal(1);
                    $ref->setCountry($country);
                    $infoLink->addReferer($ref);
                endif;
            endif;

            $dateLastClick = new \DateTime();
            $dayClick = $infoLink->getTimeLastClicked();

            // Si on a changé de jours, on ajoute une ligne
            if($dateLastClick->format("Y-m-d") > $dayClick->format("Y-m-d")):
                $lastDayClick = new DateClick();
                $lastDayClick->setNbClick($infoLink->getClicksDay());
                // On donne la date d'hier
                $dateLastClick->sub(new \DateInterval('P1D'));
                $lastDayClick->setDate($dateLastClick->format("Y-m-d"));
                $infoLink->setClicksDay(1);
                $infoLink->addDateClick($lastDayClick);
            else:
                $infoLink->setClicksDay($infoLink->getClicksDay() + 1);
            endif;

            $infoLink->setClicks($infoLink->getClicks() + 1);
            $infoLink->setTimeLastClicked($dateLastClick);
            $em->persist($infoLink);
            $em->flush();


            return $this->redirect($infoLink->getOriginalURL());
        }
    }
    protected function renderLogin(array $data)
    {
        $template = sprintf('FOSUserBundle:Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}

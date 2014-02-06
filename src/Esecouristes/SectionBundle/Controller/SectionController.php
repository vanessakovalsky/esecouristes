<?php

// src/Esecouristes/SectionBundle/Controller/SectionController.php

namespace Esecouristes\SectionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

//On ajoute le use pour récupérer l'entité
use Esecouristes\SectionBundle\Entity\Section;

class SectionController extends Controller
{
  public function indexAction()
  {
      $repository = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('EsecouristesSectionBundle:Section');
      $listeSections = $repository->findAll();
    return $this->render('EsecouristesSectionBundle:Section:index.html.twig', array('listeSections' => $listeSections
    ));
  }
  
  public function voirAction(Section $section)
  {
      $em = $this->getDoctrine()
                 ->getManager();
      // On récupère le repository
      $repository = $em->getRepository('EsecouristesSectionBundle:Section');
      // On récupère l'entité correspondant à l'id
      $section =$repository->find($section->getId());
   
      // On récupère la listes des agréments de la section
      $liste_sectionAgrements = $em->getRepository('EsecouristesSectionBundle:SectionAgrement')
                                   ->findBySection($section->getId());
      
      
    return $this->render('EsecouristesSectionBundle:Section:voir.html.twig', array(
      'section'  => $section,
      'liste_sectionAgrements' => $liste_sectionAgrements));
  }
  
  /**
   * @Secure(roles="ROLE_ADMIN")
   */
  public function ajouterAction()
  {
        
    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
    
    if( $this->get('request')->getMethod() == 'POST' )
    {
      // Ici, on s'occupera de la création et de la gestion du formulaire
      
      $this->get('session')->getFlashBag()->add('notice', 'Section bien enregistré');
    
      // Puis on redirige vers la page de visualisation de cet article
      return $this->redirect( $this->generateUrl('esecouristessection_voir', array('id' => 1)) );
    }
    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('EsecouristesSectionBundle:Section:ajouter.html.twig');
  }
  
  /**
   * @Secure(roles="ROLE_ADMIN")
   */
  public function modifierAction(Section $section)
  {
      // On récupère l'Entity Manager
      $em = $this->getDoctrine()
                 ->getEntityManager();
      
      // On récupère l'entité correspondant à l'id
      $section = $em->getRepository('EsecouristesSectionBundle:Section')
                    ->find($section->getId());
      
      return $this->render('EsecouristesSectionBundle:Section:modifier.html.twig', array('section' => $section,));
  }
  
  /**
   * @Secure(roles="ROLE_ADMIN")
   */
  public function supprimerAction(Section $section)
  {
      // On récupère l'Entity Manager
      $em = $this->getDoctrine()
                 ->getEntityManager();
      
      // ON récupère l'entité correspondante à $id
      $section = $em->getRepository('EsecouristesSectionBundle:Section')
                    ->find($section->getId());
      
         if( $this->get('request')->getMethod() == 'POST' )
    {
      // Ici, on s'occupera de la création et de la gestion du formulaire
      
      $this->get('session')->getFlashBag()->add('notice', 'Section bien supprimée');
    
      // Puis on redirige vers la page de visualisation de cet article
      return $this->redirect( $this->generateUrl('esecouristessection_accueil') );
    }
      return $this->render('EsecouristesSectionBundle:Section:supprimer.html.twig', array('id' => $section->getId()));
  }
}

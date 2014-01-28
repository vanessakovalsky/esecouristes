<?php

// src/Esecouristes/SectionBundle/Controller/SectionController.php

namespace Esecouristes\SectionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends Controller
{
  public function indexAction()
  {
    return $this->render('EsecouristesSectionBundle:Section:index.html.twig', array(
        'sections' => array()
    ));
  }
  
  public function voirAction($id)
  {
      $section = array(
          'id' => 2,
          'titre' => 'section de test',
          'contenu' => 'description de la section',
      );
    return $this->render('EsecouristesSectionBundle:Section:voir.html.twig', array(
      'section'  => $section,));
  }
  
  public function ajouterAction()
  {
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
    
    if( $this->get('request')->getMethod() == 'POST' )
    {
      // Ici, on s'occupera de la création et de la gestion du formulaire
      
      $this->get('session')->getFlashBag()->add('notice', 'Section bien enregistré');
    
      // Puis on redirige vers la page de visualisation de cet article
      return $this->redirect( $this->generateUrl('esecouristessection_voir', array('id' => 5)) );
    }
    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('EsecouristesSectionBundle:Section:ajouter.html.twig');
  }
  
  public function modifierAction($id)
  {
      $section = array(
          'id' => 2,
          'titre' => 'Section de test',
          'contenu' => 'description de la section',
      );
      return $this->render('EsecouristesSectionBundle:Section:modifier.html.twig', array('section' => $section,));
  }
  
  public function supprimerAction($id)
  {
      return $this->render('EsecouristesSectionBundle:Section:supprimer.html.twig', array('id' => $id));
  }
}
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
      
      // ON ajoute les champs de l'entité
      $formBuilder
              ->add('nom',              'text')
              ->add('nomLong',          'text',     array('required' => false))
              ->add('adresse',          'text')
              ->add('codePostal',       'number')
              ->add('ville',            'text')
              ->add('cedex',            'number',   array('required' => false))
              ->add('telephone',        'number')
              ->add('portableUrgence',  'number')
              ->add('fax',              'number',   array('required' => false))
              ->add('email',            'email')
              ->add('emailSecretariat', 'email',    array('required' => false))
              ->add('siteWeb',          'url',      array('required' => false));
      //On génère le formulaire
      $form = $formBuilder->getForm();
      
      // ON récupère la requete
      $request = $this->get('request');
      
       //On vérifie que la requete est de type POST
      if ($request->getMethod() == 'POST') {
          // On fait le lien Requete <-> Formulaire
          // La variable $section contient celle envoyé par le formulaire
          $form->bind($request);
          
          //On vérifie que les données entrées sont valide
          if ($form->isValid()) {
              //On enregistre en bdd notre section
              $em = $this->getDoctrine()->getManager();
              $em->persist($section);
              $em->flush();
          //On redirige vers la visualisation de la section
          return $this->redirect($this->generateUrl('esecouristessection_voir', array('id' => $section->getId())));
              
          }
      }
      
      //On passe la méthode CreateView() du formulaire à la vue afin qu'elle puisse afficher le formulaire
      return $this->render('EsecouristesSectionBundle:Section:ajouter.html.twig', array(
          'form' => $form->createView(),
      ));
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
      
            //On crée le formulaire à partir de la méthode formbuilder
      $formBuilder = $this->createFormBuilder($section);
      
      // ON ajoute les champs de l'entité
      $formBuilder
              ->add('nom',              'text')
              ->add('nomLong',          'text',     array('required' => false))
              ->add('adresse',          'text')
              ->add('codePostal',       'number')
              ->add('ville',            'text')
              ->add('cedex',            'number',   array('required' => false))
              ->add('telephone',        'number')
              ->add('portableUrgence',  'number')
              ->add('fax',              'number',   array('required' => false))
              ->add('email',            'email')
              ->add('emailSecretariat', 'email',    array('required' => false))
              ->add('siteWeb',          'url',      array('required' => false));
      //On génère le formulaire
      $form = $formBuilder->getForm();
      
      // ON récupère la requete
      $request = $this->get('request');
      
       //On vérifie que la requete est de type POST
      if ($request->getMethod() == 'POST') {
          // On fait le lien Requete <-> Formulaire
          // La variable $section contient celle envoyé par le formulaire
          $form->bind($request);
          
          //On vérifie que les données entrées sont valide
          if ($form->isValid()) {
              //On enregistre en bdd notre section
              $em = $this->getDoctrine()->getManager();
              $em->persist($section);
              $em->flush();
          //On redirige vers la visualisation de la section
          return $this->redirect($this->generateUrl('esecouristessection_voir', array('id' => $section->getId())));
              
          }
      }
      
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

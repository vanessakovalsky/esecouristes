<?php

// src/Esecouristes/SectionBundle/Controller/SectionController.php

namespace Esecouristes\SectionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends Controller
{
  public function indexAction()
  {
    return $this->render('EsecouristesSectionBundle:Section:index.html.twig');
  }
}
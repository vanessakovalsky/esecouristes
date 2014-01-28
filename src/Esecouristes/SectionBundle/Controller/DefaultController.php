<?php

namespace Esecouristes\SectionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EsecouristesSectionBundle:Default:index.html.twig', array('name' => $name));
    }
}

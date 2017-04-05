<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @Method("GET")
     */
    public function indexAction()
    {
      //  return $this->render('home:Default:home.html.twig');
    }
}

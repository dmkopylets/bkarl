<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index()
    {
       return $this->render('bkarl/home.html.twig',['a' => 458786767]);
    }
}


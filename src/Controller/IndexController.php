<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

        $objDateTime = new \DateTime('NOW');
        $dateString = $objDateTime->format('Y-m-d H:i:s');

        return $this->render('index/index.html.twig', [
            'controller_name' => $dateString,
        ]);
    }
}

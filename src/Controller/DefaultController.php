<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function ping()
    {
        return $this->json([
            'message' => 'Bienvenue sur l\'api de Swoome !'
        ]);
    }
}

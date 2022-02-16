<?php

namespace App\Controller\DefaultController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function ping()
    {
        return $this->json([
            'message' => 'pong'
        ]);
    }
}

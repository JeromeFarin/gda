<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $s = null;
        $dir = null;
        $amount = null;

        if ($request->get('s')) {
            $s = $request->get('s');

            if (preg_match('/^(.+)(>|<)([0-9]+)$/i', $request->get('s'), $matches)) {
                $s = $matches[1];
                $dir = $matches[2];
                $amount = $matches[3];
            }
        }

        return $this->render('index/index.html.twig', [
            'books' => $bookRepository->findByFilter($s, $dir, $amount),
        ]);
    }
}

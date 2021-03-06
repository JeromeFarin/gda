<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/{id}-{slug}", name="book_show_front", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        $categories = array_map(function ($category) {
            return $category->getName();
        }, $book->getCategories()->toArray());

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'categories' => $categories
        ]);
    }
}

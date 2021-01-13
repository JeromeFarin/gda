<?php

namespace App\Handler;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookHandler
{
    private EntityManagerInterface $entityManager;
    private EditorRepository $editorRepository;
    private AuthorRepository $authorRepository;

    public function __construct(EntityManagerInterface $entityManager, EditorRepository $editorRepository, AuthorRepository $authorRepository)
    {
        $this->entityManager = $entityManager;
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
    }

    public function persist(Book $book)
    {
        if ($book->getEditor()) {
            $book->setEditor($this->editorRepository->find($book->getEditor()));
        }

        if ($book->getAuthor()) {
            $book->setAuthor($this->authorRepository->find($book->getAuthor()));
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}

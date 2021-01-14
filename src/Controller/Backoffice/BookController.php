<?php

namespace App\Controller\Backoffice;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use App\Handler\BookHandler;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/book")
 * @IsGranted("ROLE_ADMIN")
 */
class BookController extends AbstractController
{
    private BookHandler $bookHandler;
    private ValidatorInterface $validator;

    public function __construct(BookHandler $bookHandler, ValidatorInterface $validator)
    {
        $this->bookHandler = $bookHandler;
        $this->validator = $validator;
    }

    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('backoffice/book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();

        if ($bookRequest = $request->request->get('book')) {
            foreach ($bookRequest['categories'] as &$idCategory) {
                $idCategory = $this->getDoctrine()->getRepository(Category::class)->find((int) $idCategory);
            }

            $request->request->set('book', $bookRequest);
        }

        $form = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Book $book */
            $book = $form->getData();
            $data = $request->request->get('book');

            foreach ($data['categories'] as $category) {
                $book->addCategory($category);
            }

            if ($this->validator->validate($book)) {
                $this->bookHandler->persist($book);

                return $this->redirectToRoute('book_index');
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookHandler->persist($book);

            return $this->redirectToRoute('book_index');
        }

        return $this->render('backoffice/book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('backoffice/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        if ($bookRequest = $request->request->get('book')) {
            foreach ($bookRequest['categories'] as &$idCategory) {
                $idCategory = $this->getDoctrine()->getRepository(Category::class)->find((int) $idCategory);
            }

            $request->request->set('book', $bookRequest);
        }

        $form = $this->createForm(BookType::class, $book)->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Book $book */
            $book = $form->getData();
            $data = $request->request->get('book');

            foreach ($data['categories'] as $category) {
                $book->addCategory($category);
            }

            if ($this->validator->validate($book)) {
                $this->bookHandler->persist($book);

                return $this->redirectToRoute('book_index');
            }
        }

        return $this->render('backoffice/book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }
}

<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Like;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/api")
 */
class LikeController extends AbstractController
{
    /**
     * @Route("/likes/book/{id}", name="api_book_likes", methods={"GET"})
     */
    public function bookLikes(Book $book): Response
    {
        return $this->json(
            $book->getLikes(),
            200,
            [],
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['books']
            ]
        );
    }

    /**
     * @Route("/likes/user/{id}", name="api_user_likes", methods={"GET"})
     */
    public function userLikes(User $user): Response
    {
        return $this->json(
            $user->getLikes(),
            200,
            [],
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['books']
            ]
        );
    }

    /**
     * @Route("/like", name="api_like", methods={"GET", "POST"})
     */
    public function like(Request $request, BookRepository $bookRepository, UserRepository $userRepository, LikeRepository $likeRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['book']) || !isset($data['user'])) {
            return $this->json("No param found", 400);
        }

        $book = $bookRepository->find((int) $data['book']);

        if (!$book) {
            return $this->json("No book found", 404);
        }

        $user = $userRepository->find((int) $data['user']);

        if (!$user) {
            return $this->json("No user found", 404);
        }

        $like = $likeRepository->findOneBy([
            'book' => $book->getId(),
            'user' => $user->getId(),
        ]);

        $manager = $this->getDoctrine()->getManager();

        if ($like) {
            $manager->remove($like);
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setBook($book);

            $manager->persist($like);
        }

        $manager->flush();

        return $this->json(true);
    }
}

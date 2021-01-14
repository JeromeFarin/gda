<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use App\Repository\EditorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFixture extends Fixture implements DependentFixtureInterface
{
    private EditorRepository $editorRepository;
    private AuthorRepository $authorRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(EditorRepository $editorRepository, AuthorRepository $authorRepository, CategoryRepository $categoryRepository)
    {
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i < 10; $i++) {
            $book = new Book();
            $book
                ->setTitle($faker->company)
                ->setSlug($faker->slug())
                ->setIsbn((int) $faker->randomNumber(5))
                ->setResume($faker->realText())
                ->setPrice($faker->randomFloat(2, 0, 200))
                ->setEditor($this->editorRepository->findAll()[$faker->numberBetween(0, 2)])
                ->setAuthor($this->authorRepository->findAll()[$faker->numberBetween(0, 2)])
                ->addCategory($this->categoryRepository->findAll()[$faker->numberBetween(0, 4)]);

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AuthorFixture::class,
            EditorFixture::class,
            CategoryFixture::class
        ];
    }
}

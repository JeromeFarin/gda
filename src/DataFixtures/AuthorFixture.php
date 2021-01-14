<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i < 3; $i++) {
            $author = new Author();
            $author
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setBirthday($faker->dateTimeBetween());

            $manager->persist($author);
        }

        $manager->flush();
    }
}

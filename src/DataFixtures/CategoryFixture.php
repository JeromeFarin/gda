<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i < 5; $i++) {
            $category = new Category();
            $category
                ->setName($faker->firstName())
                ->setSlug($faker->slug())
                ->setLevel($faker->numberBetween(0, 1));

            $manager->persist($category);
        }

        $manager->flush();
    }
}

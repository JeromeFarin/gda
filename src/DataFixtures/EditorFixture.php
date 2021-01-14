<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EditorFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i < 3; $i++) {
            $editor = new Editor();
            $editor
                ->setName($faker->company())
                ->setFoundationYear($faker->year());

            $manager->persist($editor);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\CourseLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class LevelFixtures extends Fixture
{
    private array $levels = ['debutant','professionnel'];
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        foreach($this->levels as $level){
            $courselevel = new CourseLevel();
            $courselevel->setName($level)
                ->setPrerequisite($faker->sentence(3));
            $manager->persist($courselevel);
        }


        $manager->flush();

    }
}

<?php

namespace App\DataFixtures;

use App\Entity\CourseCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture

{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
     private array $categories = ['Developpement web', 'Mecanique', 'Data Analytique', 'MPI', 'MIAGE', 'genie Civil'];
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
       foreach ($this->categories as $category) {
            $cat = new CourseCategory();
            $cat->setName($category);
            $cat->setDescription($faker->text());
            $cat->setSlug($this->slugger->slug($cat->getName()));
            $manager->persist($cat);
        }
        $manager->flush();
    }
}

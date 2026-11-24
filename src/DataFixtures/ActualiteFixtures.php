<?php

namespace App\DataFixtures;

use App\Entity\Actualite;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActualiteFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $user = array_filter(
            $manager->getRepository(User::class)->findAll(),
            fn(User $u) => in_array('ROLE_ADMIN', $u->getRoles(), true)
        );
        $faker = Factory::create('fr_FR');
        for($i = 1; $i <= 8; $i++){
            $actualite = new Actualite();
            $actualite->setName($faker->words(rand(1, 3), true))
                      ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days', 'now')))
                      ->setContent($faker->realText(rand(100, 200)))
                      ->setSlug($this->slugger->slug($actualite->getName(). '-' . $i))
                      ->setUser($faker->randomElement($user))
                      ->setImage($i.'.jpg');
            $manager->persist($actualite);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}

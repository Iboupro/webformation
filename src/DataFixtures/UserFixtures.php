<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    private object $hasher;
    public function __construct(UserPasswordHasherInterface $hasher, private  sluggerInterface $slugger)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        for ($i = 1; $i <= 7; $i++) {

            $user = new User;
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($this->slugger->slug($user->getFirstName().' '.$user->getLastName()).'@'.$faker->domainName());
            $user ->setImage($i.'.jpg')
                ->setpassword($this->hasher->hashPassword($user, 'password'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setRoles(['ROLE_USER'])
                ->setIsdisabled(false)
                ->setLastLogAt(new \DateTimeImmutable());
            $manager->persist($user);
        }

        $manager->flush();
        // Admin Ibou Diallo
        $user = new User;
        $user ->setFirstName($faker->firstName('Ibou'))
            ->setLastName('Diallo')
            ->setEmail('Ibou.diallo@gmail.com');

        $user ->setImage('8.jpg')
            ->setpassword($this->hasher->hashPassword($user, 'password'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setRoles(['ROLE_ADMIN'])
            ->setLastLogAt(new \DateTimeImmutable())
            ->setIsdisabled(false);
        $manager->persist($user);
        $manager->flush();
        // Admin John Doe
        $user = new User;
        $user ->setFirstName($faker->firstName('John'))
            ->setLastName('Doe')
            ->setEmail('John.doe@gmail.com')
         ->setImage('9.jpg')
            ->setpassword($this->hasher->hashPassword($user, 'password'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setRoles(['ROLE_ADMIN'])
            ->setLastLogAt(new \DateTimeImmutable())
            ->setIsdisabled(false);
        $manager->persist($user);
        $manager->flush();
    }

}

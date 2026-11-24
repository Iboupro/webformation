<?php

namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\CourseLevel;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
class CourseFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(CourseCategory::class)->findAll();
        $levels = $manager->getRepository(CourseLevel::class)->findAll();
       //filtrer les users de role Admin
        $user = array_filter(
            $manager->getRepository(User::class)->findAll(),
            fn(User $u) => in_array('ROLE_ADMIN', $u->getRoles(), true)
        );
        $faker = Factory::create('fr_FR');
        $durees = [
            'semaine' => [1, 12],
            'mois'    => [1, 12],
            'an'      => [1, 3],
        ];
        for($i = 1; $i <= 8; $i++){
            $cours = new Course();
            $cours->setName($faker->words(rand(1, 3), true))
                ->setSmallDescription($faker->paragraphs(rand(3, 4), true))
                ->setFullDescription($faker->paragraphs(rand(3, 4), true))
                ->setDuration($faker->randomDigitNotNull())
                ->setPrice($faker->randomFloat(2, 20, 100));
               //la duree
            $unite = $faker->randomElement(array_keys($durees));
            $nombre = $faker->numberBetween(...$durees[$unite]);

            $cours->setDuration(
                $nombre.' '.$unite.(($nombre > 1 && $unite !== 'mois') ? 's' : '')
            )
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days', 'now')))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-10 days', 'now')))
                ->setIsPublished($faker->boolean(90))
                ->setProgram($i.'.pdf')
                ->setSchedule($faker->monthName())
                ->setImage($i.'.jpg')
                ->setCategoryId($faker->randomElement($categories))
                ->setSlug($this->slugger->slug($cours->getName()))
                ->setLevelId($faker->randomElement($levels))
               ->setUser($faker->randomElement($user));
            $manager->persist($cours);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LevelFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}


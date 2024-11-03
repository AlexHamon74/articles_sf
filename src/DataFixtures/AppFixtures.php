<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const CATEGORY = ['Sport', 'Voyage', 'Jeux-vidéos'];

    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $categories = [];

        foreach(self::CATEGORY as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        for($i=0; $i<4; $i++){   
            $article = new Article();
            $article->setTitle($generator->sentence())
            ->setDescription($generator->text())
            ->setCategory($generator->randomElement($categories));
            $manager->persist($article);
        }

        $user = new User();
        $user->setEmail('bob@test.com')
            ->setName('Bob')
            ->setFirstname('Test')
            ->setRoles(['ROLE_USER'])
            ->setPassword('bob');
        $manager->persist($user);

    
        $manager->flush();
    }
}

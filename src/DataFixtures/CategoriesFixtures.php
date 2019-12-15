<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CategoriesFixtures extends Fixture
{
    /**
     * @var string
     */
    private $pictureUploadDir;

    public function __construct(string $pictureUploadDir)
    {
        $this->pictureUploadDir = $pictureUploadDir;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $nbCategories = 100;
        $user = $manager->find(User::class, 1);

        for ($i = 0; $i < $nbCategories; ++$i) {
            try {
                $pictureURL = "https://picsum.photos/id/{$i}/250/250";
                $basename = uniqid('picture_') . '.png';
                $filename = $this->pictureUploadDir . DIRECTORY_SEPARATOR . $basename;
                file_put_contents($filename, file_get_contents($pictureURL));

                $picture = new Picture();
                $picture
                    ->setFilename($basename)
                    ->setIsTemp(false)
                    ->setUser($user)
                    ->setUniqueId(uniqid())
                    ->setWidth(1)
                    ->setHeight(1);
                $manager->persist($picture);
                $manager->flush();

                $category = new Category();
                $category
                    ->setName($faker->sentence(3, false))
                    ->setCreatedBy($user)
                    ->setPicture($picture);
                $manager->persist($category);
                $manager->flush();

                echo "#{$i} saved".PHP_EOL;
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}

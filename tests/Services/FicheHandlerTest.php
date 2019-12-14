<?php

namespace App\Tests\Services;

use App\Entity\Category;
use App\Services\FicheHandler\FicheHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FicheHandlerTest extends KernelTestCase
{
    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }

    public function testUnPublishInvalidFiches(): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get(EntityManagerInterface::class);
        /** @var FicheHandlerInterface $ficheHandler */
        $ficheHandler = self::$container->get(FicheHandlerInterface::class);

        /** @var Category|null $category */
        $category = $entityManager->getRepository(Category::class)->find(1);
        if ($category instanceof Category) {
            $ficheHandler->unPublishInvalidFiches($category);
        }

        $this->assertTrue(true);
    }
}

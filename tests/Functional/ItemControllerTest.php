<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemControllerTest
 *
 * @package App\Tests\Functional
 */
class ItemControllerTest extends WebTestCase
{
    public function testGetList()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $user           = $userRepository->findOneBy(['username' => 'john']);
        $client->loginUser($user);

        $client->request('GET', '/item');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('very secure new item data', $client->getResponse()->getContent());
    }

    public function testCreate()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'john']);
        $client->loginUser($user);

        // here we use a transaction so as not to change the state of the base
        /** @var EntityManager $em */
        $em = static::$container->get(EntityManagerInterface::class);
        $em->beginTransaction();
        try {
            $data = 'very secure new item data';
            $newItemData = ['data' => $data];
            $client->request('POST', '/item', $newItemData);
            $client->request('GET', '/item');

            $this->assertResponseIsSuccessful();
            $this->assertStringContainsString('very secure new item data', $client->getResponse()->getContent());
        } catch (Exception $exception) {
        }
        $em->rollback();
    }

    public function testCreateNoDataParameter()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $user           = $userRepository->findOneBy(['username' => 'john']);
        $client->loginUser($user);

        $newItemData = ['data' => ''];
        $client->request('POST', '/item', $newItemData);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertStringContainsString('{"error":"No data parameter"}', $client->getResponse()->getContent());
    }

    public function testDelete()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $user           = $userRepository->findOneBy(['username' => 'john']);
        $client->loginUser($user);

        // here we use a transaction so as not to change the state of the base
        /** @var EntityManager $em */
        $em = static::$container->get(EntityManagerInterface::class);
        $em->beginTransaction();
        try {
            $client->request('DELETE', '/item/1');
            $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        } catch (Exception $exception) {
        }
        $em->rollback();
    }

    // @todo testUpdateNoItem
    // @todo testUpdateAccessDeny
    // @todo testUpdateNoDataParameterId
    // @todo testUpdateNoDataParameterData

    // @todo testDeleteNoItem
    // @todo testDeleteAccessDeny
    // @todo testDeleteNoDataParameter

}

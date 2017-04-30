<?php

namespace ProductBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerControllerTest extends WebTestCase
{
    public function testProductslisting()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/products');
    }

    public function testProductsbycategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/products/{category}');
    }

    public function testSingleproduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/{name}');
    }

}

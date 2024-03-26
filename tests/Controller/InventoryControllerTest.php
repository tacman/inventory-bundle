<?php

namespace PlinioCardoso\InventoryBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Requires a test database with data
 */
class InventoryControllerTest extends WebTestCase
{
    public function testShouldLoadStockDataPage()
    {
        $client = static::createClient();
        $client->request('GET', '/inventory/products/1/stocks');

        $this->assertResponseIsSuccessful();
    }
}

<?php

namespace Application\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function provideUrls()
    {
        return array(
            array('/'),
            array('/restaurant'),
            array('/login'),
            array('/sign-in/'),
            array('/reset-password/request'),
            array('/about'),
            array('/changelog'),
        );
    }

    /** @dataProvider provideUrls */
    public function testStaticPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}

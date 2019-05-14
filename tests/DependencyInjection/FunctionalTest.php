<?php


namespace Hurnell\PostcodeApiBundle\Tests;


use Hurnell\PostcodeApiBundle\HurnellPostcodeApiBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {

        $kernel = new PostcodeApiTestingKernel('test', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        $container->get('hurnell_postcode_api.service.postcode_api_client');

    }
}

class PostcodeApiTestingKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new HurnellPostcodeApiBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {

    }

}

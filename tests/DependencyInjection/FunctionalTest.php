<?php


namespace Hurnell\PostcodeApiBundle\Tests\DependencyInjection;


use Hurnell\PostcodeApiBundle\HurnellPostcodeApiBundle;
use Hurnell\PostcodeApiBundle\Service\PostcodeApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use GuzzleHttp\Client;

class FunctionalTest extends TestCase
{
    public function testServiceWiring(): void
    {
        $dummyConfig = [
            'extension' => 'hurnell_postcode_api',
            'config' => ['api_key' => 'whatever']
        ];
        $kernel = new PostcodeApiTestingKernel($dummyConfig);
        $kernel->boot();
        $container = $kernel->getContainer();
        $service = $container->get('hurnell_postcode_api.service.postcode_api_client');
        $this->assertInstanceOf(PostcodeApiClient::class, $service);
        $this->assertAttributeEquals($dummyConfig['config']['api_key'], 'apiKey', $service);
        $this->assertAttributeInstanceOf(Client::class, 'client', $service);

    }
}

class PostcodeApiTestingKernel extends Kernel
{
    private $dummyConfig;

    public function __construct(array $dummyConfig = [])
    {
        $this->dummyConfig = $dummyConfig;
        parent::__construct('test', true);
        //var_dump($this->getCacheDir());
    }

    public function registerBundles()
    {
        return [
            new HurnellPostcodeApiBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension($this->dummyConfig['extension'], $this->dummyConfig['config']);
        });

    }

}

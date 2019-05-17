<?php


namespace Hurnell\PostcodeApiBundle\Tests\DependencyInjection;


use GuzzleHttp\Client;
use Hurnell\PostcodeApiBundle\DependencyInjection\HurnellPostcodeApiExtension;
use Hurnell\PostcodeApiBundle\Service\PostcodeApiClient;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;


class HurnellPostcodeApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function afterLoadingTheContainerBuilderHasPostcodeService(): void
    {
        $this->load(['api_key' => 'my_api_key']);

        $this->assertContainerBuilderHasService(
            'hurnell_postcode_api.service.postcode_api_client',
            PostcodeApiClient::class
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'hurnell_postcode_api.service.postcode_api_client',
            0
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'hurnell_postcode_api.service.postcode_api_client',
            1,
            'my_api_key'
        );
        $this->assertContainerBuilderHasAlias(
            'Hurnell\PostcodeApiBundle\Service\PostcodeApiClient',
            'hurnell_postcode_api.service.postcode_api_client'
        );
    }

    /**
     * @test
     */
    public function afterLoadingTheContainerBuilderHasGuzzleClientService(): void
    {
        $this->load(['api_key' => 'my_api_key']);

        $this->assertContainerBuilderHasService(
            'hurnell_postcode_api.service.postcode_api_client',
            PostcodeApiClient::class
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new HurnellPostcodeApiExtension()];
    }
}

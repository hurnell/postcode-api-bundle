<?php


namespace Hurnell\PostcodeApiBundle\Tests\DependencyInjection;


use Hurnell\PostcodeApiBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testValuesAreInvalidIfRequiredValueIsNotProvided(): void
    {
        $this->assertConfigurationIsValid(
            array(
                array('api_key'=>'my_api_key')
            )
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}

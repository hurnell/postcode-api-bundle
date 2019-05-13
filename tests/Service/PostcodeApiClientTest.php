<?php

namespace Hurnell\PostcodeApiBundle\Tests\Service;

use Hurnell\PostcodeApiBundle\Exception\InvalidApiResponseException;
use Hurnell\PostcodeApiBundle\Exception\InvalidHouseNumberException;
use Hurnell\PostcodeApiBundle\Exception\InvalidNumberExtraException;
use Hurnell\PostcodeApiBundle\Exception\InvalidPostcodeException;
use Hurnell\PostcodeApiBundle\Service\PostcodeApiClient;

class PostcodeApiClientTest extends PostcodeApiClientTestMock
{

    /**
     * @var PostcodeApiClient
     */
    private $postcodeApiClient;


    public function setUp(): void
    {
        parent::setUp();
        $this->postcodeApiClient = new PostcodeApiClient($this->guzzleClient, 'api-key');
    }

    /* Check each exception in order */

    /**
     * @param $postcode
     * @throws InvalidApiResponseException
     * @throws InvalidPostcodeException
     * @dataProvider invalidPostcodeProvider
     */
    public function testInvalidPostcodeThrowsInvalidPostcodeException($postcode): void
    {
        $this->expectException(InvalidPostcodeException::class);

        $this->postcodeApiClient
            ->makeRequest(
                $postcode,
                20,
                ''
            );
    }

    /**
     * @return array
     */
    public function invalidPostcodeProvider(): array
    {
        return [
            ['201XC'],
            ['99999XX'],
            ['123456'],
            ['A2345A'],
        ];
    }

    /**
     * @throws InvalidApiResponseException
     * @throws InvalidPostcodeException
     */
    public function testStatusCodeNotFoundThrowsInvalidApiResponseException(): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(404);

        $this->expectException(InvalidApiResponseException::class);
        $this->expectExceptionMessage('The API request failed');

        $this->postcodeApiClient
            ->makeRequest(
                '2011XC',
                1,
                ''
            );
    }

    /**
     * @throws InvalidApiResponseException
     * @throws InvalidPostcodeException
     */
    public function testInvalidJsonThrowsInvalidApiResponseException(): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(200);
        $this->responseExpectsStream();
        $this->streamReturnsFileContents('invalid.json');

        $this->expectException(InvalidApiResponseException::class);
        $this->expectExceptionMessage('The API response could not be parsed');

        $this->postcodeApiClient
            ->makeRequest(
                '2011XC',
                1,
                ''
            );
    }

    /**
     * @throws InvalidApiResponseException
     * @throws InvalidPostcodeException
     */
    public function testGuzzleExceptionCausesInvalidApiResponseException(): void
    {
        $this->guzzleExpectsException();

        $this->expectException(InvalidApiResponseException::class);
        $this->expectExceptionMessage('The Guzzle client failed');

        $this->postcodeApiClient
            ->makeRequest(
                '2011XC',
                1,
                ''
            );
    }

    /**
     * @param string $postcode
     * @param int $number
     * @param string $dummy
     * @throws InvalidApiResponseException
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     * @throws InvalidPostcodeException
     * @dataProvider invalidHouseNumberProvider
     */
    public function testInvalidHouseNumberThrowsInvalidHouseNumberException(
        string $postcode,
        int $number,
        string $dummy
    ): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(200);
        $this->responseExpectsStream();
        $this->streamReturnsFileContents($dummy);

        $this->expectException(InvalidHouseNumberException::class);

        $this->postcodeApiClient
            ->makeRequest(
                $postcode,
                $number,
                ''
            )
            ->populatePostcodeModel();
    }

    /**
     * @return array
     */
    public function invalidHouseNumberProvider(): array
    {
        return [
            ['2011XC', 1, 'empty.json'],
            ['2015AG', 23, 'failed_response.json'],
        ];
    }

    /**
     * @throws InvalidApiResponseException
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     * @throws InvalidPostcodeException
     */
    public function testEmptyResponseThrowsInvalidHouseNumberException(): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(200);
        $this->responseExpectsStream();
        $this->streamReturnsFileContents('empty.json');

        $this->expectException(InvalidHouseNumberException::class);

        $this->postcodeApiClient
            ->makeRequest(
                '2011XC',
                1,
                ''
            )
            ->populatePostcodeModel();

    }


    /**
     * @param string $postcode
     * @param int $number
     * @param string $extra
     * @param $dummyResponseFile
     * @param $exceptionMessagePattern
     * @throws InvalidApiResponseException
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     * @throws InvalidPostcodeException
     * @dataProvider invalidNumberExtraValueProvider
     */
    public function testInvalidNumberExtraThrowsInvalidNumberExtraException(
        string $postcode,
        int $number,
        string $extra,
        $dummyResponseFile,
        $exceptionMessagePattern
    ): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(200);
        $this->responseExpectsStream();
        $this->streamReturnsFileContents($dummyResponseFile);

        $this->expectException(InvalidNumberExtraException::class);
        $this->expectExceptionMessageRegExp($exceptionMessagePattern);

        $this->postcodeApiClient
            ->makeRequest(
                $postcode,
                $number,
                $extra
            )
            ->populatePostcodeModel();
    }

    /**
     * @return array
     */
    public function invalidNumberExtraValueProvider(): array
    {
        return [
            ['2011XC', 20, '', 'response_postcode_extra.json', '/House number extra must be one of/'],
            ['2011XC', 20, ' ', 'response_postcode_extra.json', '/House number extra must be one of/'],
            ['2011XC', 20, 'P', 'response_postcode_extra.json', '/House number extra must be one of/'],
            ['2015AG', 40, 'CX', 'response_postcode_no_extra.json', '/House number extra must be empty/'],
        ];
    }

    /**
     * Finally check that a valid request does not throw an error and
     * does return a valid postcode model
     *
     * @param array $params
     * @param $dummyResponseFile
     * @throws InvalidApiResponseException
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     * @throws InvalidPostcodeException
     * @dataProvider validPostcodeNumberAndExtraProvider
     */
    public function testValidPostcodeNumberAndExtraReturnValidPostcodeModel(
        array $params,
        $dummyResponseFile
    ): void
    {
        $this->guzzleExpectsResponse();
        $this->responseExpectsStatusCode(200);
        $this->responseExpectsStream();
        $this->streamReturnsFileContents($dummyResponseFile);

        $model = $this->postcodeApiClient
            ->makeRequest(
                $params['postcode'],
                $params['number'],
                $params['extra']
            )
            ->populatePostcodeModel();
        $modelArray = $model->toArray();
        foreach ($params as $key => $value) {
            $this->assertArrayHasKey($key, $modelArray);
            $this->assertEquals($value, $modelArray[$key]);
        }
    }

    /**
     * @return array
     */
    public function validPostcodeNumberAndExtraProvider(): array
    {
        return [
            [[
                'street' => 'Doelstraat',
                'number' => 20,
                'extra' => 'RD',
                'postcode' => '2011XC',
                'city' => 'Haarlem',
                'province' => 'Noord-Holland'
            ], 'response_postcode_extra.json'],
            [[
                'street' => 'Doelstraat',
                'number' => 20,
                'extra' => 'ZW',
                'postcode' => '2011XC',
                'city' => 'Haarlem',
                'province' => 'Noord-Holland'
            ], 'response_postcode_extra.json'],
            [[
                'street' => 'Doelstraat',
                'number' => 20,
                'extra' => 'A',
                'postcode' => '2011XC',
                'city' => 'Haarlem',
                'province' => 'Noord-Holland'
            ], 'response_postcode_extra.json'],
            [[
                'street' => 'Krokusstraat',
                'number' => 40,
                'extra' => '',
                'postcode' => '2015AG',
                'city' => 'Haarlem',
                'province' => 'Noord-Holland'
            ], 'response_postcode_no_extra.json'],
        ];
    }
}

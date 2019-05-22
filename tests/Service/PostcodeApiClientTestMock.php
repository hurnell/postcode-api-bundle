<?php

namespace Hurnell\PostcodeApiBundle\Tests\Service;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use PHPUnit_Framework_MockObject_MockObject;

class PostcodeApiClientTestMock extends TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject|ClientInterface */
    protected $guzzleClient;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var StreamInterface
     */
    private $stream;

    private $request;

    private $guzzleException;


    public function setUp(): void
    {
        $this->guzzleClient = $this->mockGuzzleClientInterface();
        $this->request = $this->mockRequestInterface();
        $this->response = $this->mockResponseInterface();
        $this->stream = $this->mockStreamInterface();
        $this->guzzleException = $this->mockGuzzleException();

    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    private function mockGuzzleClientInterface()
    {
        return $this->createMock(ClientInterface::class);
    }

    private function mockResponseInterface()
    {
        return $this->createMock(ResponseInterface::class);
    }

    private function mockStreamInterface()
    {
        return $this->createMock(StreamInterface::class);
    }

    private function mockRequestInterface()
    {
        return $this->createMock(RequestInterface::class);
    }

    private function mockGuzzleException(): ConnectException
    {
        return new ConnectException('this is a guzzle exception', $this->request);
    }
    private function mockGuzzleApiException($statusCode): ClientException
    {
        $response = new Response($statusCode);
        return new ClientException('this is a guzzle exception', $this->request, $response);
    }


    protected function guzzleExpectsResponse(): void
    {
        $this->guzzleClient->expects($this->once())
            ->method('send')
            ->willReturn($this->response);
    }

    protected function guzzleExpectsException(): void
    {
        $this->guzzleClient->expects($this->once())
            ->method('send')
            ->willThrowException($this->guzzleException);
    }
    protected function guzzleExpectsApiException($statusCode): void
    {
        $this->guzzleClient->expects($this->once())
            ->method('send')
            ->willThrowException($this->mockGuzzleApiException($statusCode));
    }

    /**
     * @param int $statusCode
     */
    protected function responseExpectsStatusCode(int $statusCode): void
    {
        $this->response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($statusCode);

    }


    protected function responseExpectsStream(): void
    {
        $this->response->expects($this->once())
            ->method('getBody')
            ->willReturn($this->stream);
    }

    /**
     * @param string $dummyResponseFile
     */
    protected function streamReturnsFileContents(string $dummyResponseFile): void
    {
        $this->stream->expects($this->once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/../response_data/' . $dummyResponseFile));
    }


}

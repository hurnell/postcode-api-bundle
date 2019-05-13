<?php

namespace Hurnell\PostcodeApiBundle\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use Hurnell\PostcodeApiBundle\Exception\InvalidHouseNumberException;
use Hurnell\PostcodeApiBundle\Exception\InvalidApiResponseException;
use Hurnell\PostcodeApiBundle\Exception\InvalidNumberExtraException;
use Hurnell\PostcodeApiBundle\Exception\InvalidPostcodeException;
use Symfony\Component\HttpFoundation\Response;
use JsonPath\InvalidJsonException;
use JsonPath\JsonObject;
use Hurnell\PostcodeApiBundle\Model\PostcodeModel;

class PostcodeApiClient
{
    private const POSTCODE_API_URL = 'https://api.postcodeapi.nu/v2/addresses/?postcode=%s&number=%d';

    private const XPATH_ROOT = '$..addresses[%s].';

    private const XPATH_PARTS = [
        'postcode' => 'postcode',
        'street' => 'street',
        'number' => 'number',
        'city' => 'city[label]',
        'province' => 'province[label]',
        'geoCoordinates' => 'geo.center.wgs84.coordinates',
    ];

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var JsonObject
     */
    private $jsonObject;
    /**
     * @var string
     */
    private $extra;
    /**
     * @var int
     */
    private $number;

    /**
     * PostcodeClient constructor.
     * @param ClientInterface $client
     * @param string $apiKey
     */
    public function __construct(ClientInterface $client = null, string $apiKey = '')
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $postcode
     * @param $number
     * @param string $extra
     * @return PostcodeApiClient
     * @throws InvalidApiResponseException
     * @throws InvalidPostcodeException
     */
    public function makeRequest(string $postcode, $number, string $extra): self
    {
        $this->extra = strtoupper(preg_replace('/[\s-]*/', '', $extra));
        $this->number = $number;

        $header = ['X-Api-Key' => $this->apiKey];

        $cleanPostcode = strtoupper(preg_replace('/\s*/', '', $postcode));

        if (0 === preg_match('/^[1-9]{1}\d{3}[A-Z]{2}$/', $cleanPostcode)) {
            throw new InvalidPostcodeException('Postcode has incorrect format');
        }
        $url = sprintf(self::POSTCODE_API_URL, $postcode, (int)$number);
        $request = new Request('GET', $url, $header);

        try {
            $response = $this->client->send($request);
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new InvalidApiResponseException('The API request failed');
            }
            try {
                $this->jsonObject = new JsonObject($response->getBody()->getContents());
            } catch (InvalidJsonException $exception) {
                throw new InvalidApiResponseException('The API response could not be parsed');
            }
        } catch (GuzzleException $e) {
            throw new InvalidApiResponseException('The Guzzle client failed');
        }

        return $this;
    }


    /**
     * @return PostcodeModel
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     */
    public function populatePostcodeModel(): PostcodeModel
    {
        $root = $this->getXpathRoot();
        $postcodeModel = new PostcodeModel();
        $postcodeModel->setExtra($this->extra);
        foreach (self::XPATH_PARTS as $name => $path) {
            $xpath = $root . $path;
            $target = $this->jsonObject->get($xpath);
            $postcodeModel->setParam($name, $target[0]);
        }

        return $postcodeModel;
    }

    /**
     * @throws InvalidHouseNumberException
     */
    private function checkHouseNumber(): void
    {
        $xpath = sprintf(self::XPATH_ROOT, '*') . 'number';
        if (!$this->jsonObject->get($xpath)) {
            throw new InvalidHouseNumberException('This combination of house number and postcode does not exist.');
        }
    }

    /**
     * @throws InvalidNumberExtraException
     */
    private function checkExtra(): void
    {
        $xpath = '$..addresses[*][letter,addition]';
        $extras = array_filter($this->jsonObject->get($xpath));

        /* extra has a value that is not included in the returned array*/
        $nonEmptyExtraNotInArray = $this->extra !== '' && !in_array($this->extra, $extras, true);

        /* extra is empty but returned array is not */
        $extraEmptyBut = ($this->extra === '' && count($extras) !== 0);

        if ($nonEmptyExtraNotInArray || $extraEmptyBut) {
            $extraQualifier = count($extras) > 0 ? 'one of (' . implode(', ', $extras) . ')' : 'empty';
            $message = sprintf(
                'House number extra must be %s for this combination of postcode and house number.'
                , $extraQualifier);
            throw new InvalidNumberExtraException($message);
        }
    }

    /**
     * @return string
     * @throws InvalidHouseNumberException
     * @throws InvalidNumberExtraException
     */
    private function getXpathRoot(): string
    {
        $this->checkHouseNumber();
        $this->checkExtra();
        if ('' === $this->extra) {
            return sprintf(self::XPATH_ROOT, '?(@.letter == null and @.addition == null)');
        }

        $arg = sprintf('?(@.letter == "%s" or @.addition == "%s")', $this->extra, $this->extra);

        return sprintf(self::XPATH_ROOT, $arg);
    }
}

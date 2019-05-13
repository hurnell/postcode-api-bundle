<?php


namespace Hurnell\PostcodeApiBundle\Model;


class PostcodeModel
{
    /**
     * @var string
     */
    private $street;
    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $extra;
    /**
     * @var string
     */
    private $postcode;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $province;
    /**
     * @var array
     */
    private $geoCoordinates;


    public function setParam(string $name, $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     */
    public function setExtra(string $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getProvince(): string
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    /**
     * @return array
     */
    public function getGeoCoordinates(): array
    {
        return $this->geoCoordinates;
    }

    /**
     * @param array $geoCoordinates
     */
    public function setGeoCoordinates(array $geoCoordinates): void
    {
        $this->geoCoordinates = $geoCoordinates;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


}

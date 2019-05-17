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
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
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
    public function getNumber(): ?int
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
    public function getExtra(): ?string
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
    public function getPostcode(): ?string
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
    public function getCity(): ?string
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
    public function getProvince(): ?string
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
    public function getGeoCoordinates(): ?array
    {
        return $this->geoCoordinates;
    }

    /**
     * @param array $geoCoordinates
     */
    public function setGeoCoordinates(array $geoCoordinates): void
    {
        if (is_array($geoCoordinates) && count($geoCoordinates) === 2) {
            $this->geoCoordinates = array_combine(['latitude', 'longitude'], $geoCoordinates);
        }
    }

    public function getFlattenedGeoCoordinates(): string
    {
        $flat = '';
        if (is_array($this->geoCoordinates) && count($this->geoCoordinates) === 2) {
            /* note that using %s instead of %f avoids precision errors */
            return sprintf('%s,%s', $this->geoCoordinates['longitude'], $this->geoCoordinates['latitude']);
        }
        return $flat;
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

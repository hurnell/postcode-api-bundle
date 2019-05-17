<?php


namespace Hurnell\PostcodeApiBundle\Tests\Model;


use Hurnell\PostcodeApiBundle\Model\PostcodeModel;
use PHPUnit\Framework\TestCase;

class PostcodeModelTest extends TestCase
{
    public function testModelIsEmptyWhenInstantiated():void
    {
        $model = new PostcodeModel();
        $this->assertNull($model->getStreet());
        $this->assertNull($model->getNumber());
        $this->assertNull($model->getPostcode());
        $this->assertNull($model->getCity());
        $this->assertNull($model->getProvince());
        $this->assertEquals('', $model->getFlattenedGeoCoordinates());
        $this->assertArrayHasKey('street', $model->toArray());
        $this->assertJson($model->toJson());
    }
}

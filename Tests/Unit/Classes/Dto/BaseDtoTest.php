<?php
namespace Oktopuce\SiteGenerator\Dto;

use PHPunit\Framework\TestCase;

/**
 * Tests for base DTO
 *
 */
class BaseDtoTest extends TestCase
{
    /**
     * @test
     */
    public function testGetTitleSanitize()
    {
        /** @var BaseDto $baseDto */
        $baseDto = new BaseDto;

        $title = 'é_jfez6(43)0à÷¡÷©ëd©';
        $baseDto->setTitle($title);

        // Test title sanitation for folder creation
        $this->assertEquals($baseDto->getTitleSanitize(), "-jfez6-43-0-d-");
    }

}

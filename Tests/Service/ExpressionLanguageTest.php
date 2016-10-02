<?php

namespace Jall\AopMonologBundle\Service;

class ExpressionLanguageTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Jall\AopMonologBundle\Service\ExpressionLanguage
     */
    private $expressionLanguage;

    public function setUp()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @small
     */
    public function testCountFunctionExists()
    {
        $result = $this->expressionLanguage->evaluate('count(["test","strings","here"])');
        $this->assertEquals(3, $result);
    }

}

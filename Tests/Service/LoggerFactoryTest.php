<?php

namespace Jall\AopMonologBundle\Tests\Service;

use Jall\AopMonologBundle\Service\LoggerFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var \Jall\AopMonologBundle\Service\LoggerFactory
     */
    private $loggerFactory;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects($this->any())->method($this->anything())->willReturnCallback(function ($channel) {
            static $loggers = [];

            if (!isset($loggers[$channel])) {
                $loggers[$channel] = $this->createMock(LoggerInterface::class);
            }

            return $loggers[$channel];
        });
        $this->loggerFactory = new LoggerFactory($this->container);
    }

    /**
     * @small
     */
    public function testReturnsDifferentLoggers()
    {
        $phpLogger = $this->loggerFactory->get('php');
        $this->assertInstanceOf(LoggerInterface::class, $phpLogger);

        $securityLogger = $this->loggerFactory->get('security');
        $this->assertInstanceOf(LoggerInterface::class, $securityLogger);

        $this->assertNotSame($phpLogger, $securityLogger);
    }

    /**
     * @small
     */
    public function testRequestingSameLoggerReturnsSameInstance()
    {
        $phpLogger = $this->loggerFactory->get('php');
        $this->assertInstanceOf(LoggerInterface::class, $phpLogger);

        $phpLogger2 = $this->loggerFactory->get('php');
        $this->assertInstanceOf(LoggerInterface::class, $phpLogger2);

        $this->assertSame($phpLogger, $phpLogger2);
    }

}

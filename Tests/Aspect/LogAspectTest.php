<?php

namespace Jall\AopMonologBundle\Aspect;

use Go\Aop\Intercept\MethodInvocation;
use Go\Aop\Support\AnnotatedReflectionMethod;
use Jall\AopMonologBundle\Annotation\Log;
use Jall\AopMonologBundle\Annotation\LogException;
use Jall\AopMonologBundle\Service\ExpressionLanguage;
use Jall\AopMonologBundle\Service\LoggerFactory;
use Psr\Log\LoggerInterface;

class LogAspectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var \Jall\AopMonologBundle\Service\LoggerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerFactory;

    /**
     * @var \Jall\AopMonologBundle\Service\ExpressionLanguage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $expressionLanguage;

    /**
     * @var \Jall\AopMonologBundle\Aspect\LogAspect
     */
    private $logAspect;

    /**
     * @var \Jall\AopMonologBundle\Annotation\Log|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logAnnotation;

    /**
     * @var \Jall\AopMonologBundle\Annotation\LogException|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logExceptionAnnotation;

    /**
     * @var \Go\Aop\Support\AnnotatedReflectionMethod|\PHPUnit_Framework_MockObject_MockObject
     */
    private $annotatedMethod;

    /**
     * @var \Go\Aop\Intercept\MethodInvocation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $methodInvocation;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->loggerFactory = $this->createMock(LoggerFactory::class);
        $this->loggerFactory->expects($this->any())->method($this->anything())->willReturn($this->logger);

        $this->expressionLanguage = new ExpressionLanguage();

        $this->logAspect = new LogAspect(
            $this->loggerFactory,
            $this->expressionLanguage
        );

        $this->logAnnotation = $this->createMock(Log::class);
        $this->logExceptionAnnotation = $this->createMock(LogException::class);

        $this->annotatedMethod = $this->createMock(AnnotatedReflectionMethod::class);
        $this->annotatedMethod->expects($this->any())->method('getAnnotation')->willReturnMap([
            [Log::class, $this->logAnnotation],
            [LogException::class, $this->logExceptionAnnotation],
        ]);

        $this->methodInvocation = $this->createMock(MethodInvocation::class);
        $this->methodInvocation->expects($this->any())->method('getMethod')->willReturn($this->annotatedMethod);
    }

    /**
     * @small
     */
    public function testLogMethodAlwaysLogsOnNonException()
    {
        $this->setFunctionArguments(['user' => 'admin']);
        $this->setLogAnnotationParameters();

        $this->logger->expects($this->once())->method('log')->with('notice', 'Correct log!', ['{{ user }}' => 'admin']);

        $this->logAspect->logMethod($this->methodInvocation);
    }

    /**
     * @small
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Admins not allowed!
     */
    public function testLogMethodDoesNotLogOnException()
    {
        $this->setFunctionArguments(['user' => 'admin']);
        $this->setLogAnnotationParameters();

        $this->throwExceptionInFunctionCall(new \InvalidArgumentException('Admins not allowed!'));

        // The logger should never be called in this scenario.
        $this->logger->expects($this->never())->method($this->anything());

        $this->logAspect->logMethod($this->methodInvocation);
    }

    /**
     * @small
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Admins not allowed!
     */
    public function testLogExceptionMethodLogsOnException()
    {
        $this->setFunctionArguments(['user' => 'admin']);
        $this->setLogExceptionAnnotationParameters();

        $exception = new \InvalidArgumentException('Admins not allowed!');
        $this->throwExceptionInFunctionCall($exception);

        $this->logger->expects($this->once())->method('log')->with('notice', 'Correct log!', [
            '{{ user }}' => 'admin',
            'exception' => $exception,
        ]);

        $this->logAspect->logMethodException($this->methodInvocation);
    }

    /**
     * @small
     */
    public function testLogExceptionMethodDoesNotLogWhenNoExceptionThrown()
    {
        $this->setFunctionArguments(['user' => 'admin']);
        $this->setLogExceptionAnnotationParameters();

        $this->logger->expects($this->never())->method($this->anything());

        $this->logAspect->logMethodException($this->methodInvocation);
    }

    private function setLogAnnotationParameters(array $parameters = [])
    {
        $message = isset($parameters['message']) ? $parameters['message'] : 'Correct log!';
        $level = isset($parameters['level']) ? $parameters['level'] : 'notice';
        $channel = isset($parameters['channel']) ? $parameters['channel'] : 'php';
        $context = isset($parameters['context']) ? $parameters['context'] : ['{{ user }}' => 'input["user"]'];

        $this->logAnnotation->expects($this->any())->method('getMessage')->willReturn($message);
        $this->logAnnotation->expects($this->any())->method('getLevel')->willReturn($level);
        $this->logAnnotation->expects($this->any())->method('getChannel')->willReturn($channel);
        $this->logAnnotation->expects($this->any())->method('getContext')->willReturn($context);
    }

    private function setLogExceptionAnnotationParameters(array $parameters = [])
    {
        $message = isset($parameters['message']) ? $parameters['message'] : 'Correct log!';
        $level = isset($parameters['level']) ? $parameters['level'] : 'notice';
        $channel = isset($parameters['channel']) ? $parameters['channel'] : 'php';
        $context = isset($parameters['context']) ? $parameters['context'] : ['{{ user }}' => 'input["user"]'];

        $this->logExceptionAnnotation->expects($this->any())->method('getMessage')->willReturn($message);
        $this->logExceptionAnnotation->expects($this->any())->method('getLevel')->willReturn($level);
        $this->logExceptionAnnotation->expects($this->any())->method('getChannel')->willReturn($channel);
        $this->logExceptionAnnotation->expects($this->any())->method('getContext')->willReturn($context);
    }

    private function setFunctionArguments(array $arguments)
    {
        $parameters = array_map(function ($parameter) {
            $reflection = $this->createMock(\ReflectionParameter::class);
            $reflection->expects($this->any())->method('getName')->willReturn($parameter);
            return $reflection;
        }, array_keys($arguments));

        $this->annotatedMethod->expects($this->any())->method('getParameters')->willReturn($parameters);
        $this->methodInvocation->expects($this->any())->method('getArguments')->willReturn(array_values($arguments));
    }

    private function throwExceptionInFunctionCall(\Exception $e)
    {
        $this->methodInvocation->expects($this->any())->method('proceed')->willThrowException($e);

    }

}

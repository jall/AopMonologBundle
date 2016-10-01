<?php

namespace Jall\AopMonologBundle\Aspect;

use Jall\AopMonologBundle\Annotation\Log as LogAnnotation;
use Jall\AopMonologBundle\Annotation\LogException as LogExceptionAnnotation;
use Jall\AopMonologBundle\Service\ExpressionLanguage;
use Jall\AopMonologBundle\Service\LoggerFactory;
use Go\Aop\Aspect as AspectInterface;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;

class LogAspect implements AspectInterface
{

    /**
     * @var \Jall\AopMonologBundle\Service\LoggerFactory
     */
    private $loggerFactory;

    /**
     * @var \Jall\AopMonologBundle\Service\ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct(
        LoggerFactory $loggerFactory,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->loggerFactory = $loggerFactory;
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * @Around("@execution(Jall\AopMonologBundle\Annotation\Log)")
     *
     * @param \Go\Aop\Intercept\MethodInvocation $invocation
     *
     * @return mixed
     *   Whatever the intercepted method returns.
     */
    public function logMethod(MethodInvocation $invocation)
    {
        $annotation = $invocation->getMethod()->getAnnotation(LogAnnotation::class);

        $result = $invocation->proceed();

        $values = [
            'input' => $this->getArguments($invocation),
            'output' => $result,
        ];

        $context = $this->parseContext($annotation->getContext(), $values);

        $logger = $this->loggerFactory->get($annotation->getChannel());
        $logger->log($annotation->getLevel(), $annotation->getMessage(), $context);

        return $result;
    }

    /**
     * @Around("@execution(Jall\AopMonologBundle\Annotation\LogException)")
     *
     * @param \Go\Aop\Intercept\MethodInvocation $invocation
     *
     * @return mixed
     *     Whatever the intercepted method returns, if it doesn't throw an exception.
     *
     * @throws \Exception
     */
    public function logMethodException(MethodInvocation $invocation)
    {
        try {
            return $invocation->proceed();
        } catch (\Exception $e) {
            $annotation = $invocation->getMethod()->getAnnotation(LogExceptionAnnotation::class);

            $values = [
                'input' => $this->getArguments($invocation),
                'exception' => $e,
            ];

            $context = $this->parseContext($annotation->getContext(), $values) + ['exception' => $e];

            $logger = $this->loggerFactory->get($annotation->getChannel());
            $logger->log($annotation->getLevel(), $annotation->getMessage(), $context);

            throw $e;
        }
    }

    /**
     * @param \Go\Aop\Intercept\MethodInvocation $invocation
     *
     * @return array
     */
    private function getArguments(MethodInvocation $invocation)
    {
        $arguments = [];
        $data = $invocation->getArguments();

        foreach ($invocation->getMethod()->getParameters() as $delta => $parameter) {
            $arguments[$parameter->getName()] = $data[$delta];
        }

        return $arguments;
    }

    /**
     * @param string[] $rawContexts
     * @param array $values
     *
     * @return array
     */
    private function parseContext($rawContexts, $values)
    {
        $contexts = [];

        foreach ($rawContexts as $key => $rawContext) {
            $contexts[$key] = $this->expressionLanguage->evaluate($rawContext, $values);
        }

        return $contexts;
    }

}

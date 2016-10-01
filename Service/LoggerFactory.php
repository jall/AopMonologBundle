<?php

namespace Jall\AopMonologBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Helper class to retrieve loggers for various channels.
 *
 * This is a helper service to ease testing as logging to multiple/arbitrary
 * channels requires the service container to be injected.
 */
class LoggerFactory
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $channel
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function get($channel)
    {
        $logger_class = "monolog.logger.{$channel}";

        return $this->container->get($logger_class);
    }

}

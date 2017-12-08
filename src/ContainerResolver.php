<?php declare(strict_types=1);

namespace Ellipse\Middleware;

use Psr\Container\ContainerInterface;

use Interop\Http\Server\MiddlewareInterface;

class ContainerResolver
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
    */
    private $container;

    /**
     * Sets up the container resolver with the given resolver and delegate.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param callable                          $delegate
     */
    public function __construct(ContainerInterface $container, callable $delegate)
    {
        $this->container = $container;
        $this->delegate = $delegate;
    }

    /**
     * Create a middleware from the given class name or proxy the delegate.
     *
     * @param mixed $element
     * @return \Interop\Http\Server\MiddlewareInterface
     */
    public function __invoke($element): MiddlewareInterface
    {
        if (is_string($element) && is_a($element, MiddlewareInterface::class, true)) {

            return new ContainerMiddleware($this->container, $element);

        }

        return ($this->delegate)($element);
    }
}

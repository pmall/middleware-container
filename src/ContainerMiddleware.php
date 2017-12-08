<?php declare(strict_types=1);

namespace Ellipse\Middleware;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Container\ReflectionContainer;
use Ellipse\Container\OverriddenContainer;

class ContainerMiddleware implements MiddlewareInterface
{
    /**
     * The container.
     *
     * @var \Ellipse\Container\ReflectionContainer
    */
    private $container;

    /**
     * The middleware class name.
     *
     * @var string
     */
    private $class;

    /**
     * Set up a container middleware with the given container and middleware
     * class name.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $class
     */
    public function __construct(ContainerInterface $container, string $class)
    {
        $this->container = $container;
        $this->class = $class;
    }

    /**
     * Get the middleware from the container and proxy its process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface      $request
     * @param \Interop\Http\Server\RequestHandlerInterface  $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = new ReflectionContainer(
            new OverriddenContainer($this->container, [
                ServerRequestInterface::class => $request,
                RequestHandlerInterface::class => $handler,
            ])
        );

        $middleware = $container->get($this->class);

        return $middleware->process($request, $handler);
    }
}

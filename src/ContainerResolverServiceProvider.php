<?php declare(strict_types=1);

namespace Ellipse\Middleware;

use Interop\Container\ServiceProviderInterface;

class ContainerResolverServiceProvider implements ServiceProviderInterface
{
    public function getFactories()
    {
        return [];
    }

    public function getExtensions()
    {
        return [
            'ellipse.resolvers.middleware' => function ($container, callable $previous = null) {

                $previous = $previous ?: function ($element) {

                    return $element;

                };

                return new ContainerResolver($container, $previous);

            },
        ];
    }
}

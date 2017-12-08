<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;
use function Eloquent\Phony\Kahlan\onStatic;

use Psr\Container\ContainerInterface;

use Interop\Http\Server\MiddlewareInterface;

use Ellipse\Container\ReflectionContainer;

use Ellipse\Middleware\ContainerResolver;
use Ellipse\Middleware\ContainerMiddleware;

describe('ContainerResolver', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class)->get();

        $this->delegate = stub();

        $this->resolver = new ContainerResolver($this->container, $this->delegate);

    });

    describe('->__invoke()', function () {

        context('when the given element is a middleware class name', function () {

            it('should return a ContainerMiddleware', function () {

                $element = onStatic(mock(MiddlewareInterface::class))->className();

                $test = ($this->resolver)($element);

                $middleware = new ContainerMiddleware($this->container, $element);

                expect($test)->toEqual($middleware);

            });

        });

        context('when the given element is not a string', function () {

            it('should proxy the delegate', function () {

                $element = new class {};

                $middleware = mock(MiddlewareInterface::class)->get();

                $this->delegate->with($element)->returns($middleware);

                $test = ($this->resolver)($element);

                expect($test)->toBe($middleware);

            });

        });

        context('when the given element is not a middleware class name', function () {

            it('should proxy the delegate', function () {

                $element = 'class';

                $middleware = mock(MiddlewareInterface::class)->get();

                $this->delegate->with($element)->returns($middleware);

                $test = ($this->resolver)($element);

                expect($test)->toBe($middleware);

            });

        });

    });

});

<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;

use Ellipse\Container\ReflectionContainer;
use Ellipse\Middleware\ContainerMiddleware;

describe('ContainerMiddleware', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->middleware = new ContainerMiddleware($this->container->get(), 'class');

    });

    it('should implement MiddlewareInterface', function () {

        expect($this->middleware)->toBeAnInstanceOf(MiddlewareInterface::class);

    });

    describe('->process()', function () {

        it('should get the middleware from the container and proxy its ->process() method', function () {

            $request = mock(ServerRequestInterface::class)->get();
            $response = mock(ResponseInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $middleware = mock(MiddlewareInterface::class);

            $this->container->get->with('class')->returns($middleware);

            $middleware->process->with($request, $handler)->returns($response);

            $test = $this->middleware->process($request, $handler);

            expect($test)->toBe($response);

        });

    });

});

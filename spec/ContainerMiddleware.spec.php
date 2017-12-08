<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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

describe('ContainerMiddleware', function () {

    beforeAll(function () {

        class TestMiddleware implements MiddlewareInterface
        {
            private $dependency1;

            public function __construct(TestDependency1 $dependency1, TestDependency2 $dependency2)
            {
                $this->dependency1 = $dependency1;
            }

            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                if ($this->dependency1 == new TestDependency1($request, $handler)) {

                    return mock(ResponseInterface::class)->get();

                }
            }
        }

        class TestDependency1
        {
            private $request;
            private $handler;

            public function __construct(ServerRequestInterface $request, RequestHandlerInterface $handler)
            {
                $this->request = $request;
                $this->handler = $handler;
            }
        }

        class TestDependency2
        {
            //
        }

    });

    describe('->process()', function () {

        it('should proxy the middleware ->process method by injecting the request and the handler', function () {

            $dependency2 = new TestDependency2;

            $container = mock(ContainerInterface::class);

            $exception = mock([Throwable::class, NotFoundExceptionInterface::class])->get();

            $container->get->with(TestMiddleware::class)->throws($exception);
            $container->get->with(TestDependency1::class)->throws($exception);
            $container->get->with(TestDependency2::class)->returns($dependency2);

            $request = mock(ServerRequestInterface::class)->get();
            $handler = mock(RequestHandlerInterface::class)->get();

            $middleware = new ContainerMiddleware($container->get(), TestMiddleware::class);

            $test = $middleware->process($request, $handler);

            expect($test)->toBeAnInstanceOf(ResponseInterface::class);

        });

    });

});

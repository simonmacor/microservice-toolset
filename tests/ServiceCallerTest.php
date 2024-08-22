<?php

namespace SimonMacor\MicroserviceToolset\Tests;

use GuzzleHttp\Client;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use SimonMacor\MicroserviceToolset\Context;
use SimonMacor\MicroserviceToolset\Exception\ServiceNotFound;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InternalErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InvalidParamsException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InvalidRequestException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\MethodNotFoundException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\ParseErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\ServerErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;
use SimonMacor\MicroserviceToolset\JsonRpc\Response;
use SimonMacor\MicroserviceToolset\ServiceCaller;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use SimonMacor\MicroserviceToolset\ServicesRegistry\ServiceConfiguration;
use SimonMacor\MicroserviceToolset\ServicesRegistry\ServicesRegistryInterface;

class ServiceCallerTest extends TestCase
{
    use ProphecyTrait;

    public function testServiceCallerReturnJsonRpcResponse(): void
    {
        $serviceConfiguration = new ServiceConfiguration(
            "testService",
            "https://testservice",
            "secret"
        );

        $serviceRegistry = $this->prophesize(ServicesRegistryInterface::class);
        $serviceRegistry
            ->getConfigurationByServiceName('testService')
            ->willReturn($serviceConfiguration);


        $context = $this->prophesize(Context::class);
        $context->getId()->willReturn('testContextId');
        $context->getPrincipal()->willReturn('testContextPrincipal');

        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn('{"jsonrpc": "2.0", "result": {"id": 2602, "body": "content"}, "id": "testContextId"}');

        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $client = $this->prophesize(Client::class);
        $client->sendRequest(Argument::any())->willReturn($response->reveal());

        $testedInstance = new ServiceCaller(
            $client->reveal(),
            $serviceRegistry->reveal(),
            $this->prophesize(LoggerInterface::class)->reveal(),
            $context->reveal()
        );

        $this->assertInstanceOf(Response::class,
            $testedInstance->callService('testService', 'test.action', ['test' => ['id' => 20000]])
        );
    }
    public function testServiceNotFound(): void
    {
        $this->expectException(ServiceNotFound::class);

        $serviceRegistry = $this->prophesize(ServicesRegistryInterface::class);
        $serviceRegistry->getConfigurationByServiceName('testService')->willReturn(null);


        $testedInstance = new ServiceCaller(
            $this->prophesize(Client::class)->reveal(),
            $serviceRegistry->reveal(),
            $this->prophesize(LoggerInterface::class)->reveal(),
            $this->prophesize(Context::class)->reveal()
        );

        $testedInstance->callService('testService', 'test.action', []);
    }


    public static function handleJsonRpcErrorResponseProvider(): iterable
    {
        return [
            'parse_error' => [
                ParseErrorException::class,
                JsonRpcError::ParseError->code(),
                JsonRpcError::ParseError->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": "testContextId"}'
            ],
            'invalid_request' => [
                InvalidRequestException::class,
                JsonRpcError::InvalidRequest->code(),
                JsonRpcError::InvalidRequest->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": "testContextId"}'
            ],
            'invalid_params' => [
                InvalidParamsException::class,
                JsonRpcError::InvalidParams->code(),
                JsonRpcError::InvalidParams->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32602, "message": "Invalid params"}, "id": "testContextId"}'
            ],
            'method_not_found' => [
                MethodNotFoundException::class,
                JsonRpcError::MethodNotFound->code(),
                JsonRpcError::MethodNotFound->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "testContextId"}'
            ],
            'internal_error' => [
                InternalErrorException::class,
                JsonRpcError::InternalError->code(),
                JsonRpcError::InternalError->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32603, "message": "Internal error"}, "id": "testContextId"}'
            ],
            'server_error' => [
                ServerErrorException::class,
                -32001,
                JsonRpcError::ServerError->message(),
                '{"jsonrpc": "2.0", "error": {"code": -32001, "message": "Server error"}, "id": "testContextId"}'
            ],
        ];
    }

    /**
     * @dataProvider handleJsonRpcErrorResponseProvider
     */
    public function testHandleJsonRpcErrorResponse(
        string $expectedException,
        int $expectedExceptionCode,
        string $expectedExceptionMessage,
        string $jsonResponse
    ): void
    {
        $this->expectException($expectedException);
        $this->expectExceptionCode($expectedExceptionCode);
        $this->expectExceptionMessage($expectedExceptionMessage);


        $serviceConfiguration = new ServiceConfiguration(
            "testService",
            "https://testservice",
            "secret"
        );

        $serviceRegistry = $this->prophesize(ServicesRegistryInterface::class);
        $serviceRegistry
            ->getConfigurationByServiceName('testService')
            ->willReturn($serviceConfiguration);


        $context = $this->prophesize(Context::class);
        $context->getId()->willReturn('testContextId');
        $context->getPrincipal()->willReturn('testContextPrincipal');

        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($jsonResponse);

        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $client = $this->prophesize(Client::class);
        $client->sendRequest(Argument::any())->willReturn($response->reveal());

        $testedInstance = new ServiceCaller(
            $client->reveal(),
            $serviceRegistry->reveal(),
            $this->prophesize(LoggerInterface::class)->reveal(),
            $context->reveal()
        );

        $testedInstance->callService('testService', 'test.action', ['test' => ['id' => 20000]]);
    }
}

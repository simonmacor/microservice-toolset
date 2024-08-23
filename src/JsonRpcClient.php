<?php

declare(strict_types=1);

namespace MicroserviceToolset;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use MicroserviceToolset\Exception\ServiceNotFound;
use MicroserviceToolset\JsonRpc\Exception\InternalErrorException;
use MicroserviceToolset\JsonRpc\Exception\InvalidParamsException;
use MicroserviceToolset\JsonRpc\Exception\InvalidRequestException;
use MicroserviceToolset\JsonRpc\Exception\MethodNotFoundException;
use MicroserviceToolset\JsonRpc\Exception\ParseErrorException;
use MicroserviceToolset\JsonRpc\Exception\ServerErrorException;
use MicroserviceToolset\JsonRpc\Exception\UnknownErrorException;
use MicroserviceToolset\JsonRpc\JsonRpcError;
use MicroserviceToolset\JsonRpc\Request as JsonRpcRequest;
use MicroserviceToolset\JsonRpc\Response;
use MicroserviceToolset\ServicesRegistry\ServiceConfiguration;
use MicroserviceToolset\ServicesRegistry\ServicesRegistryInterface;

class JsonRpcClient
{
    /**
     * @param array<string> $paramsToMask
     */
    public function __construct(
        private readonly Client $client,
        private readonly ServicesRegistryInterface $serviceRegistry,
        private readonly LoggerInterface $logger,
        private readonly Context $context,
        private readonly string $path = "/jsonrpc",
        private readonly array $paramsToMask = ["password"],
    ) {
    }

    /**
     * @param array<string, mixed> $params
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function callService(string $serviceName, string $method, array $params, int $version = 1): Response
    {
        $serviceConfiguration = $this->serviceRegistry->getConfigurationByServiceName($serviceName);
        if (!$serviceConfiguration instanceof ServiceConfiguration) {
            throw new ServiceNotFound($serviceName);
        }

        $JsonRpcRequest = new JsonRpcRequest($this->context->getId(), $method.'.'.$version, $params);

        $this->logger->info("request sent", $this->buildLogContextFromJsonRpcRequest($JsonRpcRequest));

        $response = $this->client->sendRequest($this->buildRequest($JsonRpcRequest, $serviceConfiguration));
        $content = $response->getBody()->getContents();

        $this->logger->info("response received", $this->buildLogContextFromHttpResponse($content));

        /** @var array<string, mixed> $decodedContent */
        $decodedContent = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $this->buildJsonRpcResponse($decodedContent);
    }

    private function buildRequest(JsonRpcRequest $JsonRpcRequest, ServiceConfiguration $serviceConfiguration): RequestInterface
    {
        return new Request(
            "POST",
            $serviceConfiguration->getAddress().$this->path,
            [
                'Authorization' => 'Bearer '.$serviceConfiguration->getSecret(),
                'Content-Type' => 'application/json',
            ],
            $JsonRpcRequest->toJson()
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLogContextFromJsonRpcRequest(JsonRpcRequest $request): array
    {
        $requestAsArray = $request->toArray();
        foreach ($this->paramsToMask as $key) {
            if (is_array($requestAsArray['params']) && array_key_exists($key, $requestAsArray['params'])) {
                $requestAsArray['params'][$key] = '****';
            }
        }

        return [
            "request" => $requestAsArray,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLogContextFromHttpResponse(?string $responseContent): array
    {
        return ["response" => $responseContent];
    }

    /**
     * @param array<string, mixed> $responseAsArray
     */
    private function buildJsonRpcResponse(array $responseAsArray): Response
    {
        $this->handleJsonRpcErrorResponse($responseAsArray);

        return Response::fromDecodedJson($responseAsArray);
    }

    /**
     * @param array<string, mixed> $responseAsArray
     */
    private function handleJsonRpcErrorResponse(array $responseAsArray): void
    {
        if ($responseAsArray["jsonrpc"] == JsonRpcRequest::JSON_RPC_VERSION
            && isset($responseAsArray["id"])
            && (isset($responseAsArray["error"])
                && is_array($responseAsArray["error"])
                && $this->validateJsonRpcErrorObject($responseAsArray["error"]))
        ) {

            if (is_array(JsonRpcError::ServerError->code())
                && in_array($responseAsArray["error"]["code"], JsonRpcError::ServerError->code())
            ) {
                throw new ServerErrorException((int)$responseAsArray["error"]["code"]);
            }

            match ($responseAsArray["error"]["code"]) {
                JsonRpcError::ParseError->code() => throw new ParseErrorException(),
                JsonRpcError::InvalidParams->code() => throw new InvalidParamsException(),
                JsonRpcError::InvalidRequest->code() => throw new InvalidRequestException(),
                JsonRpcError::MethodNotFound->code() => throw new MethodNotFoundException(),
                JsonRpcError::InternalError->code() => throw new InternalErrorException(),
                default => throw new UnknownErrorException(),
            };
        }
    }

    /**
     * @param array<string, mixed> $jsonRpcError
     */
    private function validateJsonRpcErrorObject(array $jsonRpcError): bool
    {
        return ($jsonRpcError["code"] === JsonRpcError::ParseError->code() && $jsonRpcError['message'] === JsonRpcError::ParseError->message())
        || ($jsonRpcError["code"] === JsonRpcError::InvalidRequest->code() && $jsonRpcError['message'] === JsonRpcError::InvalidRequest->message())
        || ($jsonRpcError["code"] === JsonRpcError::InvalidParams->code() && $jsonRpcError['message'] === JsonRpcError::InvalidParams->message())
        || ($jsonRpcError["code"] === JsonRpcError::MethodNotFound->code() && $jsonRpcError['message'] === JsonRpcError::MethodNotFound->message())
        || ($jsonRpcError["code"] === JsonRpcError::InternalError->code() && $jsonRpcError['message'] === JsonRpcError::InternalError->message())
        || (is_array(JsonRpcError::ServerError->code()) && in_array($jsonRpcError["code"], JsonRpcError::ServerError->code()) && $jsonRpcError['message'] === JsonRpcError::ServerError->message())
        ;
    }
}
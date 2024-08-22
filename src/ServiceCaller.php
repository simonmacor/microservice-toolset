<?php

declare(strict_types=1);

namespace SimonMacor\MicroserviceToolset;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use SimonMacor\MicroserviceToolset\Exception\ServiceNotFound;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InternalErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InvalidParamsException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\InvalidRequestException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\MethodNotFoundException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\ParseErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\ServerErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\Exception\UnknownErrorException;
use SimonMacor\MicroserviceToolset\JsonRpc\JsonRpcError;
use SimonMacor\MicroserviceToolset\JsonRpc\Request as JsonRpcRequest;
use SimonMacor\MicroserviceToolset\JsonRpc\Response;
use SimonMacor\MicroserviceToolset\ServicesRegistry\ServiceConfiguration;
use SimonMacor\MicroserviceToolset\ServicesRegistry\ServicesRegistryInterface;

class ServiceCaller
{
    public function __construct(
        private Client $client,
        private ServicesRegistryInterface $serviceRegistry,
        private LoggerInterface $logger,
        private Context $context,
        private string $path = "/jsonrpc",
        private array $paramsToMask = ["password"],
    ) {
    }

    public function callService(string $serviceName, string $method, array $params, int $version = 1): Response
    {
        $serviceConfiguration = $this->serviceRegistry->getConfigurationByServiceName($serviceName);
        if ($serviceConfiguration === null) {
            throw new ServiceNotFound($serviceName);
        }

        $id = $this->context->getId() ?? uniqid();
        $JsonRpcRequest = new JsonRpcRequest($id, $method.'.'.$version, $params);

        $this->logger->info("request sent", $this->buildLogContextFromJsonRpcRequest($JsonRpcRequest));

        $response = $this->client->sendRequest($this->buildRequest($JsonRpcRequest, $serviceConfiguration));
        $content = $response->getBody()->getContents();
        $this->logger->info("response received", $this->buildLogContextFromHttpResponse($content));

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

    private function buildLogContextFromJsonRpcRequest(JsonRpcRequest $request): array
    {
        $payload = $request->toArray();
        foreach ($this->paramsToMask as $key) {
            if (array_key_exists($key, $payload['params'])) {
                $payload['params'][$key] = '****';
            }
        }

        return [
            "context" => [
                "correlation_id" => $this->context->getId(),
                "principal" => $this->context->getPrincipal(),
            ],
            "payload" => $payload,
        ];
    }

    private function buildLogContextFromHttpResponse(?string $responseContent): array
    {
        return [
            "context" => [
                "correlation_id" => $this->context->getId(),
                "principal" => $this->context->getPrincipal(),
            ],
            "response" => $responseContent,
        ];
    }

    private function buildJsonRpcResponse(array $responseAsArray): Response
    {
        $this->handleJsonRpcErrorResponse($responseAsArray);

        return Response::fromDecodedJson($responseAsArray);
    }

    private function handleJsonRpcErrorResponse(array $responseAsArray): void
    {
        if ($responseAsArray["jsonrpc"] == JsonRpcRequest::JSON_RPC_VERSION
            && isset($responseAsArray["id"])
            && (isset($responseAsArray["error"]) && $this->validateJsonRpcErrorObject($responseAsArray["error"]))
        ) {

            if (in_array($responseAsArray["error"]["code"], JsonRpcError::ServerError->code())) {
                throw new ServerErrorException((int)$responseAsArray["error"]["code"]);
            }

            match ($responseAsArray["error"]["code"]) {
                JsonRpcError::ParseError->code() => throw new ParseErrorException(),
                JsonRpcError::InvalidParams->code() => throw new InvalidParamsException(),
                JsonRpcError::InvalidRequest->code() => throw new InvalidRequestException(),
                JsonRpcError::MethodNotFound->code() => throw new MethodNotFoundException(),
                JsonRpcError::InternalError->code() => throw new InternalErrorException(),
            };
        }
    }

    private function validateJsonRpcErrorObject(array $jsonRpcError): bool
    {
        return ($jsonRpcError["code"] === JsonRpcError::ParseError->code() && $jsonRpcError['message'] === JsonRpcError::ParseError->message())
        || ($jsonRpcError["code"] === JsonRpcError::InvalidRequest->code() && $jsonRpcError['message'] === JsonRpcError::InvalidRequest->message())
        || ($jsonRpcError["code"] === JsonRpcError::InvalidParams->code() && $jsonRpcError['message'] === JsonRpcError::InvalidParams->message())
        || ($jsonRpcError["code"] === JsonRpcError::MethodNotFound->code() && $jsonRpcError['message'] === JsonRpcError::MethodNotFound->message())
        || ($jsonRpcError["code"] === JsonRpcError::InternalError->code() && $jsonRpcError['message'] === JsonRpcError::InternalError->message())
        || (in_array($jsonRpcError["code"], JsonRpcError::ServerError->code()) && $jsonRpcError['message'] === JsonRpcError::ServerError->message())
        ;
    }
}
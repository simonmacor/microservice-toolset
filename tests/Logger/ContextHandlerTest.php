<?php

namespace MicroserviceToolset\Tests\Logger;


use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use MicroserviceToolset\Context;
use MicroserviceToolset\Logger\ContextHandler;
use Prophecy\PhpUnit\ProphecyTrait;

class ContextHandlerTest extends TestCase
{
    use ProphecyTrait;
    public function testHandleSuccess()
    {
        $principal = 'testPrincipal';
        $requestId = 'testRequestId';
        $extra = 123456;

        $record = new LogRecord(
            \DateTimeImmutable::createFromFormat('U', time()),
            'test',
            Level::Info,
            'test'
        );

        $context = $this->prophesize(Context::class);
        $context->getPrincipal()->willReturn($principal);
        $context->getId()->willReturn($requestId);
        $context->getExtra()->willReturn(['extra' => $extra]);

        $handler = new ContextHandler(context: $context->reveal(), bubble: false);

        $this->assertTrue($handler->handle($record));
    }
}

<?php

declare(strict_types=1);

namespace MicroserviceToolset\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;
use MicroserviceToolset\Context;

class ContextHandler extends StreamHandler
{
    /**
     * @param Context $context
     * @param resource|string $stream
     * @param int|string|Level $level
     * @param bool $bubble
     */
    public function __construct(private Context $context, $stream = 'php://stdout', int|string|Level $level = Level::Info, bool $bubble = true)
    {
        parent::__construct($stream, $level, $bubble);
    }

    public function handle(LogRecord $record): bool
    {
        $context = [];

        foreach ($record->context as $key => $value) {
            $context[$key] = $value;
        }

        foreach ($this->context->getExtra() as $key => $value) {
            $context[$key] = $value;
        }

        $context['correlation_id'] = $this->context->getId();
        $context['principal'] = $this->context->getPrincipal();

        return parent::handle($record->with(context: $context));
    }
}

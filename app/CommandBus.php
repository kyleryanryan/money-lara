<?php

namespace App;

use Illuminate\Support\Facades\App;
use ReflectionClass;
use InvalidArgumentException;

class CommandBus
{
    public function handle($command)
    {
        $reflection = new ReflectionClass($command);

        $commandClassName = $reflection->getShortName();
        if (!str_ends_with($commandClassName, 'Command')) {
            throw new InvalidArgumentException("The provided command does not follow the naming convention of ending with 'Command'.");
        }

        $namespace = $reflection->getNamespaceName();
        $handlerName = str_replace('Command', 'CommandHandler', $commandClassName);
        $handlerClass = str_replace('Commands', 'Handlers', $namespace) . '\\' . $handlerName;
    
        if (!class_exists($handlerClass)) {
            throw new InvalidArgumentException("Handler class [{$handlerClass}] does not exist.");
        }

        // resolve or instantiate a class
        $handler = App::make($handlerClass);

        return $handler($command);
    }
}

<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

use Gianfriaur\Serializer\Exception\Attribute\MissingParameterException;

abstract class AbstractSerializeAttribute
{

    abstract function injectMetadata(): array;

    abstract function validate();

    private function hasParameter(string $name): bool
    {
        return $this->{$name} !== null;
    }

    protected function hasParametersOrThrowException(array $names):void
    {
        foreach ($names as $name){
            if (!$this->hasParameter($name)){
                throw new MissingParameterException($this::class, $name);
            }
        }
    }

}
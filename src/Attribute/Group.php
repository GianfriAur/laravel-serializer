<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Gianfriaur\Serializer\Attribute;

use Gianfriaur\Serializer\Exception\Attribute\GroupAttributeMalformed;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Group extends AbstractSerializeAttribute
{
    public function __construct(
        public string $name,
        public array  $parameters,
    )
    {
        parent::__construct(null, null);
    }

    protected function metadataMergeRecursive(...$arrays)
    {
        $merged = array();
        while ($arrays) {
            $array = array_shift($arrays);
            if (!$array)
                continue;
            foreach ($array as $key => $value)
                if (is_string($key))
                    if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                        $merged[$key] = $this->metadataMergeRecursive($merged[$key], $value);
                    else
                        $merged[$key] = $value;
                else
                    $merged[] = $value;
        }
        return (is_array($merged) && $merged === array_filter($merged, 'is_string')) ? array_unique($merged, SORT_REGULAR) : $merged;
    }

    public function injectMetadata(): array
    {
        $metadata = [$this->name => ['properties' => []]];

        $normalized_attributes = [];

        foreach ($this->parameters as $key => $parameter) {

            //manage [ 0 => new Class() , 1 => 4, 2 => null , ... ]
            if (is_numeric($key) && !is_string($parameter)) {
                throw new GroupAttributeMalformed('In the attribute \'Group\' the parameter \'parameters\' can only contain string or array es:  #[Group( ... , parameters: [ \'attr\', \'attr_2\' => [] ] )]');
                // throw exception
            } //manage [ 0 => '' , 1 => '' , .... ]
            elseif (is_numeric($key) && is_string($parameter)) {
                $normalized_attributes[] = new Get($parameter, $parameter, [$this->name]);
                $normalized_attributes[] = new Set($parameter, $parameter, [$this->name]);
                $normalized_attributes[] = new Name($parameter, $parameter, [$this->name]);

            } //manage [ '' => '' , '' => '' , .... ]
            elseif (is_string($key) && is_string($parameter)) {
                $normalized_attributes[] = new Get($parameter, $key, [$this->name]);
                $normalized_attributes[] = new Set($parameter, $key, [$this->name]);
                $normalized_attributes[] = new Name($parameter, $key, [$this->name]);
            } //manage [ '' => AbstractSerializeAttribute::class , '' => AbstractSerializeAttribute::class , .... ]
            elseif (is_string($key) && $parameter instanceof AbstractSerializeAttribute) {
                if ($parameter instanceof Group) {
                    throw new GroupAttributeMalformed('In the attribute \'Group\' the parameter \'parameters\' can\'t contains it self');
                } else {
                    $parameter->parameter_name = $key;
                    $parameter->ref_groups = [$this->name];
                    $normalized_attributes[] = $parameter;
                }
            }

            if (is_string($key) && is_array($parameter)) {

                foreach ($parameter as $parameter_element) {

                    if (!($parameter_element instanceof AbstractSerializeAttribute)) {
                        throw new GroupAttributeMalformed('In the attribute \'Group\' the parameter \'parameters\' can contain only AbstractSerializeAttribute es:  #[Group( ... , parameters: [ \'attr\', \'attr_2\' => [ new Name( ... ) ] ] )]');
                    } elseif ($parameter_element instanceof Group) {
                        throw new GroupAttributeMalformed('In the attribute \'Group\' the parameter \'parameters\' can\'t contains it self');
                    } else {
                        $parameter_element->parameter_name = $key;
                        $parameter_element->ref_groups = [$this->name];
                        $normalized_attributes[] = $parameter_element;
                    }
                }

            }
        }

        foreach ($normalized_attributes as $normalized_attribute) {
            $normalized_attribute->validate();
            $metadata = $this->metadataMergeRecursive($metadata, $normalized_attribute->injectMetadata());
        }

        return $metadata;
    }

    public function validate()
    {
        $this->hasParametersOrThrowException([]);
        return true;
    }

}
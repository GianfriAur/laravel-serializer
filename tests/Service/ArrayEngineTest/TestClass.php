<?php

namespace Gianfriaur\Serializer\Tests\Service\ArrayEngineTest;

class TestClass
{
    private ?TestClass $son = null;

    public function __construct(
        public readonly string $name,
        private readonly string $surname,
        private readonly int $nested_level =0
    )
    {
        if ($this->nested_level > 0){
            $this->son = new TestClass(
                $this->nested_level."_".$this->name,
                $this->nested_level."_".$this->name,
                $this->nested_level-1
            );
        }
    }

    public function getSurname():string{
        return $this->surname;
    }

    public function seyHi($name):string{
        return 'Hi '.$name;
    }

    public function getSon():?TestClass{
        return $this->son;
    }
}
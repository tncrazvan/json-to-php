<?php
namespace App;

readonly class CustomArrayProperty implements CustomProperty {
    public static function create(
        CustomPrimitiveProperty|CustomClassProperty $item,
        int $nested,
    ):self {
        return new self(
            item:$item,
            nested:$nested,
        );
    }

    private function __construct(
        public CustomPrimitiveProperty|CustomClassProperty $item,
        public int $nested,
    ) {
    }

    public function getDefinition(): string {
        return $this->item->getDefinition();
    }
    private static function nest(string $value, int $times) {
        $value = "array<$value>";
        $times--;
        if ($times < 1) {
            return $value;
        }
        return self::nest(
            value:$value,
            times:$times,
        );
    }

    public function toStringForAnnotation() {
        if ($this->item instanceof CustomClassProperty) {
            $typePrefix = stringSnakeToPascal($this->item->prefix);
        } else {
            $typePrefix = '';
        }
        
        $nestedClassName = self::nest(
            value:$typePrefix.$this->item->type,
            times:$this->nested,
        );
        return <<<PHP
             * @param $nestedClassName \${$this->item->name}
            PHP;
    }

    public function toStringForCreate(): string {
        return <<<PHP
            array \${$this->item->name},
            PHP;
    }

    public function toStringForConstructor(): string {
        return <<<PHP
            public array \${$this->item->name},
            PHP;
    }
}
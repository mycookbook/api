<?php

declare(strict_types=1);

namespace App\Utils;

class IngredientMaker
{
    protected $ingredients;

    /**
     * @param $items
     */
    public function __construct($items = [])
    {
        //todo: validate
        $this->ingredients = [
            "data" => $items
        ];
    }

    /** @phpstan-ignore-next-line  */
    private function validate()
    {
        //todo:
    }

    public static function format($items, $format = 'json')
    {
        $maker = new IngredientMaker($items);

        if ($format == 'json') {
            return $maker->toJson();
        }
    }

    private function toJson()
    {
        return json_encode($this->ingredients);
    }

    /** @phpstan-ignore-next-line  */
    private function toArray()
    {
        if (is_array($this->ingredients)) {
            return $this->ingredients;
        }

        return json_decode($this->ingredients);
    }

    /** @phpstan-ignore-next-line  */
    private function toString()
    {
        //todo
    }

    /** @phpstan-ignore-next-line  */
    private function toObject()
    {
        //todo
    }
}

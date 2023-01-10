<?php

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

    private function validate()
    {
        //todo:
    }

    /**
     * @param $items
     * @param $format
     * @return false|string|void
     */
    public static function format($items, $format = 'json')
    {
        $maker = new IngredientMaker($items);

        if ($format == 'json') {
            return $maker->toJson();
        }
    }

    /**
     * @return false|string
     */
    private function toJson()
    {
        return json_encode($this->ingredients);
    }

    /**
     * @return array|mixed
     */
    private function toArray()
    {
        if (is_array($this->ingredients)) {
            return $this->ingredients;
        }

        return json_decode($this->ingredients);
    }

    private function toString()
    {
        //todo
    }

    private function toObject()
    {
        //todo
    }
}

<?php

namespace Moloni\Traits;

trait ClassTrait
{
    private $name = '';

    private function loadName()
    {
        if (empty($this->name)) {
            $name = explode('\\', get_class($this));
            $name = end($name);
            $name = strtolower($name);

            $this->name = $name;
        }
    }

    protected function className()
    {
        $this->loadName();

        return $this->name;
    }
}

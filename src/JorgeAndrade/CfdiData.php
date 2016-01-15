<?php namespace JorgeAndrade;

use JorgeAndrade\Exceptions\CfdiException;
use JorgeAndrade\Validator;

abstract class CfdiData
{
    protected function add($property, $value)
    {
        $this->{$property} = $value;
    }

    protected function parseData(array $data)
    {
        $validator = new Validator($data, $this->rules());
        $validator->make();

        foreach ($data as $property => $value) {
            $this->verifyValidProperty($property);
            $this->add($property, $value);
        }
        return true;
    }

    protected function verifyValidProperty($property)
    {
        if (!array_key_exists($property, $this->rules())) {
            throw new CfdiException(sprintf("The '%s' isnt a valid property from SAT Schema", $property));
        }
    }

    public function __get($property)
    {
        return $this->{$property};
    }

    public function getData()
    {
        return get_object_vars($this);
    }

    abstract public function rules();
}

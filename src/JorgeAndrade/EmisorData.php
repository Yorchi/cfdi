<?php namespace JorgeAndrade;

class EmisorData extends CfdiData
{
    protected $rfc;

    protected $nombre;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "rfc" => '',
            "nombre" => '',
        ];
    }
}

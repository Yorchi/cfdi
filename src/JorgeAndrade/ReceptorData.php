<?php namespace JorgeAndrade;

class ReceptorData extends CfdiData
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
            "rfc" => 'required',
            "nombre" => 'required',
        ];
    }
}

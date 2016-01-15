<?php namespace JorgeAndrade;

class ImpuestosRetenidosData extends CfdiData
{
    protected $impuesto;

    protected $importe;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "impuesto" => 'required',
            "importe" => 'required',
        ];
    }
}

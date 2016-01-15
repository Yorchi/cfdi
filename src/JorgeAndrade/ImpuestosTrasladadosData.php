<?php namespace JorgeAndrade;

class ImpuestosTrasladadosData extends CfdiData
{
    protected $impuesto;

    protected $tasa;

    protected $importe;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "impuesto" => 'required',
            "tasa" => 'required',
            "importe" => 'required',
        ];
    }
}

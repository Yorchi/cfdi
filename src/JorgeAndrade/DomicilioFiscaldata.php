<?php namespace JorgeAndrade;

class DomicilioFiscalData extends CfdiData
{
    protected $calle;

    protected $noExterior;

    protected $noInterior;

    protected $colonia;

    protected $municipio;

    protected $estado;

    protected $pais;

    protected $codigoPostal;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "calle" => 'required',
            "noExterior" => 'required',
            "colonia" => 'required',
            "municipio" => 'required',
            "estado" => 'required',
            "pais" => 'required',
            "codigoPostal" => 'required',
        ];
    }
}

<?php namespace JorgeAndrade;

class ConceptosData extends CfdiData
{
    protected $cantidad;

    protected $unidad;

    protected $descripcion;

    protected $valorUnitario;

    protected $importe;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "cantidad" => 'required',
            "unidad" => 'required',
            "descripcion" => 'required',
            "valorUnitario" => 'required',
            "importe" => 'required',
        ];
    }
}

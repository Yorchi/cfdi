<?php namespace JorgeAndrade;

class GeneralData extends CfdiData
{
    protected $version = '3.2';

    protected $serie;

    protected $folio;

    protected $fecha;

    protected $sello;

    protected $formaDePago;

    protected $noCertificado;

    protected $certificado;

    protected $condicionesDePago;

    protected $subTotal;

    protected $descuento;

    protected $MotivoDescuento;

    protected $TipoCambio;

    protected $Moneda;

    protected $total;

    protected $tipoDeComprobante;

    protected $metodoDePago;

    protected $LugarExpedicion;

    protected $NumCtaPago;

    protected $FolioFiscalOrig;

    protected $SerieFolioFiscalOrig;

    protected $FechaFolioFiscalOrig;

    protected $MontoFolioFiscalOrig;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "serie" => '',
            "folio" => '',
            "fecha" => '',
            "sello" => '',
            "formaDePago" => 'required',
            "noCertificado" => 'required',
            "certificado" => '',
            "condicionesDePago" => '',
            "subTotal" => 'required',
            "descuento" => '',
            "MotivoDescuento" => '',
            "TipoCambio" => '',
            "Moneda" => 'required',
            "total" => 'required',
            "tipoDeComprobante" => 'required',
            "metodoDePago" => 'required',
            "LugarExpedicion" => '',
            "NumCtaPago" => 'required',
            "FolioFiscalOrig" => '',
            "SerieFolioFiscalOrig" => '',
            "FechaFolioFiscalOrig" => '',
            "MontoFolioFiscalOrig" => '',
        ];
    }
}

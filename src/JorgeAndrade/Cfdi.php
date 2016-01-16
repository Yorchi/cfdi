<?php
namespace JorgeAndrade;

error_reporting(E_ALL ^ E_WARNING);

use DOMdocument;
use JorgeAndrade\Contracts\CfdiTypeInterface;
use JorgeAndrade\Exceptions\CfdiException;
use XSLTProcessor;

class Cfdi
{
    protected $xml;

    protected $comprobante;

    protected $emisor;

    protected $domicilioFiscal;

    protected $regimen;

    protected $receptor;

    protected $conceptos;

    protected $impuestos;

    protected $traslados;

    protected $retenciones;

    protected $totalImpuestosTrasladados = 0;

    protected $totalImpuestosRetenidos = 0;

    protected $urlToFile = "http://www.sat.gob.mx/cfd/3/cadenaoriginal_3_2/cadenaoriginal_3_2.xslt";

    protected $cadena_original;

    protected $sello;

    protected $certificado;

    protected $cer;

    protected $key;

    public function __construct(CfdiTypeInterface $type, $cer, $key, $charset = "UTF-8")
    {
        $this->xml = new DOMdocument("1.0", $charset);
        $this->comprobante = $this->xml->appendChild(
            $this->xml->createElement('cfdi:Comprobante')
        );
        $this->setAttribute($this->comprobante, $type->getAttributes());

        $this->cer = $cer;
        $this->key = $key;
    }

    protected function setAttribute($nodo, $attributes)
    {
        foreach ($attributes as $key => $val) {
            $val = preg_replace('/\s\s+/', ' ', $val);
            $val = trim($val);
            if (strlen($val) > 0) {
                $val = utf8_encode(str_replace("|", "/", $val));
                $nodo->setAttribute($key, $val);
            }
        }
    }

    public function add($data, $nodo = null)
    {
        $method = 'add' . $this->prepareMethod(get_class($data));
        try {
            $this->{$method}($data, $nodo);
        } catch (CfdiException $e) {
            return $e->getMessage();
        }
        return $data;
    }

    protected function prepareMethod($class)
    {
        $object = explode('\\', $class);
        return $object[1];
    }

    protected function addGeneralData($data, $nodo)
    {
        $this->setAttribute($this->comprobante, $data->getData());
    }

    protected function addEmisorData($data, $nodo)
    {
        $this->emisor = $this->comprobante->appendChild($this->xml->createElement("cfdi:Emisor"));
        $this->setAttribute($this->emisor, $data->getData());
    }

    protected function addDomicilioFiscalData($data, $nodo)
    {
        $element = $nodo == 'emisor' ? 'DomicilioFiscal' : 'Domicilio';
        $this->domicilioFiscal = $this->{$nodo}->appendChild($this->xml->createElement("cfdi:{$element}"));
        $this->setAttribute($this->domicilioFiscal, $data->getData());
    }

    protected function addRegimenFiscalData($data, $nodo)
    {
        $this->regimen = $this->emisor->appendChild($this->xml->createElement("cfdi:RegimenFiscal"));
        $this->setAttribute($this->regimen, $data->getData());
    }

    protected function addReceptorData($data, $nodo)
    {
        $this->receptor = $this->comprobante->appendChild($this->xml->createElement("cfdi:Receptor"));
        $this->setAttribute($this->receptor, $data->getData());
    }

    protected function addConceptosData($data, $nodo)
    {
        if (is_null($this->conceptos)) {
            $this->conceptos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Conceptos"));
        }

        $concepto = $this->conceptos->appendChild($this->xml->createElement("cfdi:Concepto"));
        $this->setAttribute($concepto, $data->getData());

    }

    protected function addImpuestosTrasladadosData($data, $nodo)
    {
        if (is_null($this->impuestos)) {
            $this->impuestos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Impuestos"));
        }

        if (is_null($this->traslados)) {
            $this->traslados = $this->impuestos->appendChild($this->xml->createElement("cfdi:Traslados"));
        }

        $traslado = $this->traslados->appendChild($this->xml->createElement("cfdi:Traslado"));
        $this->setAttribute($traslado, $data->getData());

        $this->totalImpuestosTrasladados += $traslado->getAttribute('importe');

        $this->setAttribute($this->impuestos, [
            "totalImpuestosTrasladados" => number_format($this->totalImpuestosTrasladados, 2, '.', ''),
        ]);

    }

    protected function addImpuestosRetenidosData($data, $nodo)
    {
        if (is_null($this->impuestos)) {
            $this->impuestos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Impuestos"));
        }

        if (is_null($this->retenciones)) {
            $this->retenciones = $this->impuestos->appendChild($this->xml->createElement("cfdi:Retenciones"));
        }

        $retencion = $this->retenciones->appendChild($this->xml->createElement("cfdi:Retencion"));
        $this->setAttribute($retencion, $data->getData());

        $this->totalImpuestosRetenidos += $retencion->getAttribute('importe');

        $this->setAttribute($this->impuestos, [
            "totalImpuestosRetenidos" => number_format($this->totalImpuestosRetenidos, 2, '.', ''),
        ]);
    }

    public function getCadenaOriginal()
    {
        if (!is_null($this->cadena_original)) {
            return $this->cadena_original;
        }

        $xsl = new DOMDocument;
        $xsl->load($this->urlToFile);
        $procesador = new XSLTProcessor;
        $procesador->importStyleSheet($xsl);
        $paso = new DOMDocument;
        $paso->loadXML($this->xml());
        return $this->cadena_original = $procesador->transformToXML($paso);
    }

    protected function setSello()
    {
        $pkeyid = openssl_get_privatekey(file_get_contents($this->key));
        openssl_sign($this->getCadenaOriginal(), $crypttext, $pkeyid, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyid);

        $this->comprobante->setAttribute("sello", $this->sello = base64_encode($crypttext));

        $this->comprobante->setAttribute("certificado", $this->getCertificado());
    }

    public function getCertificado()
    {
        if (!is_null($this->certificado)) {
            return $this->certificado;
        }

        return $this->parseCertificado();
    }

    public function getSello()
    {
        return $this->sello;
    }

    protected function parseCertificado()
    {
        $datos = file($this->cer);
        for ($i = 0; $i < sizeof($datos); $i++) {
            if (strstr($datos[$i], "END CERTIFICATE") || strstr($datos[$i], "BEGIN CERTIFICATE")) {
                continue;
            }
            $this->certificado .= trim($datos[$i]);
        }

        return $this->certificado;
    }

    public function xml()
    {
        $this->xml->formatOutput = true;
        return $this->xml->saveXML();
    }

    protected function parseName()
    {
        $serie = $this->comprobante->getAttribute('serie') ?: 'F';
        $folio = $this->comprobante->getAttribute('folio') ?: uniqid();
        return $serie . $folio . '.xml';
    }

    public function save($path, $name = null)
    {
        $this->setSello();
        $name = is_null($name) ? $this->parseName() : $name;
        $this->xml->formatOutput = true;
        if ($this->xml->save($xml = $path . $name)) {
            return $xml;
        }
        return false;
    }
}

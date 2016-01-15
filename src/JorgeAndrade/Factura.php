<?php

namespace JorgeAndrade;

use DOMdocument;
use InvalidArgumentException;
use JorgeAndrade\Contracts\CfdiTypeInterface;
use JorgeAndrade\Exceptions\CfdiException;

class Factura implements CfdiTypeInterface
{   
    public function getAttributes()
    {
        return [
            'xmlns:cfdi' => 'http://www.sat.gob.mx/cfd/3',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd'
        ];
    }
}

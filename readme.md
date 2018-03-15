# JorgeAndrade\CFDI

Este paquete te permite generar los xml para la generacion de cfdi en mexico.

- [Introducción](#introduccion)
- [Instalación](#instalacion)
- [Uso](#uso)
  - [Agregando datos](#agregando-datos)
  - [Emisor](#emisor)
  - [Regimen Fiscal](#regimen-fiscal)
  - [Receptor](#receptor)
  - [Domicilio Fiscal](#domicilio-fiscal)
  - [Conceptos](#conceptos)
  - [Impuestos Trasladados](#impuestos-trasladados)
  - [Impuestos Retenidos](#impuestos-retenidos)
  - [Crear xml](#crear-xml)
  - [Todo list](#todo-list)
- [Licencia](#licencia)


## Introducción
CFDI te permite generar el xml para el proceso de timbrado de un CFDI (Comprobante Fiscal Digital por Internet), facturacion electronica en mexio

## Instalación
Simplemente instala el paquete con composer:

```php
composer require jorgeandrade/cfdi
```
Una vez composer termine de instalar el paquete simplemente importa el paquete y crea una nueva instancia pasando los parametros correspondientes:

```php
require 'vendor/autoload.php';

use JorgeAndrade\Cfdi;
use JorgeAndrade\ConceptosData;
use JorgeAndrade\DomicilioFiscalData;
use JorgeAndrade\EmisorData;
use JorgeAndrade\Exceptions\CfdiException;
use JorgeAndrade\Factura;
use JorgeAndrade\GeneralData;
use JorgeAndrade\ImpuestosTrasladadosData;
use JorgeAndrade\ImpuestosRetenidosData;
use JorgeAndrade\ReceptorData;
use JorgeAndrade\RegimenFiscalData;

$key = getcwd() . "/csds/AAD990814BP7.key.pem";
$cer = getcwd() . "/csds/AAD990814BP7.cer.pem";
$cfdi = new Cfdi(new Factura, $cer, $key);

try {

} catch (CfdiException $e) {
    var_dump($e->getMessage());
}
```

## Uso
Crear un xml es extremadamente facil.
Si algo sale mal las funciones arrojaran una exception de tipo `JorgeAndrade\Exceptions\CfdiException`.

Crea una nueva instancia de `Cfdi` y pasale los parametros correspondientes:
- tipo de comprobante: Factura; `new Factura`
- cer: certificado en formato pem
- key: llave privada en formato pem

Por el momento solo tenemos `Factura` como tipo de comprobante, esta en desarrollo: `Nomina`, `Contabilidad`.

```php
$key = getcwd() . "/csds/AAD990814BP7.key.pem";
$cer = getcwd() . "/csds/AAD990814BP7.cer.pem";
$cfdi = new Cfdi(new Factura, $cer, $key);
```

## Agregando datos
Para agregar datos al xml, CFDI cuenta con un metodo llamado `add`, pasando 2 posibles parametros:
- una instancia de algun objeto que extienda de CfdiData : `ConceptosData`, `DomicilioFiscalData`, `EmisorData`, `GeneralData`, `ImpuestosTrasladadosData`, `ImpuestosRetenidosData`, `ReceptorData`, `RegimenFiscalData`
- Valor obligatorio solo para establecer el Domicio fiscal del emisor o receptor, valores permitidos: `emisor` y `receptor`

```php
$cfdi->add(
  new GeneralData([
    'serie' => 'F',
    'folio' => 1,
    'fecha' => date("Y-m-d\TH:i:s"),
    'formaDePago' => 'Pago en una sola Exhibición',
    'noCertificado' => '20001000000200000293',
    'subTotal' => '2000.00',
    'Moneda' => 'MXN',
    'total' => '2320.00',
    'tipoDeComprobante' => 'ingreso',
    'metodoDePago' => 'Efectivo',
    'LugarExpedicion' => 'CD de Mexico',
    'NumCtaPago' => 'No identificado',
  ]);
);
```

La informacion guardada en los objetos CfdiData debe ser del tipo `(array)`, y estos deben ser acordes al anexo 20 del SAT.

## Emisor
```php
$cfdi->add(
  new EmisorData([
      'rfc' => 'AAD990814BP7',
      'nombre' => 'John Doe del Socorro',
  ])
);
```

## Regimen Fiscal
```php
$cfdi->add(
  new RegimenFiscalData([
    'Regimen' => 'Ley de pequeñas y medianas empresas',
  ])
);
```

## Receptor
```php
$cfdi->add(
  new ReceptorData([
    'rfc' => 'AAD990814BP7',
    'nombre' => 'Jane Doe',
  ])
);
```

## Domicilio Fiscal
```php
$tipo = 'emisor'; //'emisor' o 'receptor'
$cfdi->add(
  new DomicilioFiscalData([
    'calle' => 'Insurgente',
    'noExterior' => '600',
    'colonia' => 'Centro',
    'municipio' => 'CD de Mexico',
    'estado' => 'Mexico',
    'pais' => 'Mexico',
    'codigoPostal' => '99000',
  ])
  , 'emisor'
);

$cfdi->add(
  new DomicilioFiscalData([
    'calle' => 'Insurgente',
    'noExterior' => '600',
    'colonia' => 'Centro',
    'municipio' => 'CD de Mexico',
    'estado' => 'Mexico',
    'pais' => 'Mexico',
    'codigoPostal' => '99000',
  ])
  , 'receptor'
);
```

## Conceptos
```php
$cfdi->add(
  new ConceptosData([
    'cantidad' => '1',
    'unidad' => 'NO APLICA',
    'descripcion' => 'Dominio .com',
    'valorUnitario' => '2000.00',
    'importe' => '2000.00',
  ])
);
```

## Impuestos Trasladados
```php
$cfdi->add(
  new ImpuestosTrasladadosData([
    'impuesto' => 'IVA',
    'tasa' => '16.00',
    'importe' => '320.00',
  ])
);
```

## Impuestos Retenidos
```php
$cfdi->add(
  new ImpuestosRetenidosData([
    'impuesto' => 'IVA',
    'importe' => '320.00',
  ])
);
```

## Crear xml
Paara generar el xml usaremos el metodo `save`, pasando dos parametros:
- $path; requerido. La ruta donde se guardara el archivo: `getcwd(). '/xmls/'`;
- $name; opcional. El nombre del xml: **'F1.xml';** Si este no se especifica el nombre seta tomado en base a la serie y el folio del xml si estan presentes, si no, se usara **F** y un numero aleatorio: **F1234123431241.xml**

#### Valores devueltos
La ruta del archivo o `false` en caso de error.
```php
if ($xml = $cfdi->save($path)) {
  echo 'Comprobante creado en: ' . $xml;
}
```

### Todo list

- [ ] Tipo de comprobante: Nomina
- [ ] Tipo de comprobante: Contabilidad
- [ ] Tipo de datos: Adendas
- [ ] Tipo de datos: Complementos
- [ ] Tipo de datos: Percepciones
- [ ] Tipo de datos: Deducciones
- [ ] Tipo de datos: Horas extras
- [ ] Tipo de datos: Incapacidades


## Licencia

Csd programa de codigo abierto bajo la licencia [MIT license](http://opensource.org/licenses/MIT)
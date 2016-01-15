<?php

namespace spec\JorgeAndrade;

use JorgeAndrade\Factura;
use PhpSpec\ObjectBehavior;

class CfdiSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new Factura);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('JorgeAndrade\Cfdi');
    }

}

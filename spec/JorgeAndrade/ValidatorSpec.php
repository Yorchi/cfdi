<?php

namespace spec\JorgeAndrade;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
    function let()
    {
        $data = [
            "serie" => "Hola"
        ];

        $rules = [
            "serie" => "required"
        ];

        $this->beConstructedWith($data, $rules);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('JorgeAndrade\Validator');
    }

    function it_should_make_pass_data_if_required_rule()
    {
        $data = [
            "serie" => "Hola"
        ];

        $rules = [
            "serie" => "required"
        ];
        $this->beConstructedWith($data, $rules);
        $this->make()->shouldReturn(true);
    }

    function it_should_throw_an_exception_if_required_field_isnt()
    {
        $data = [
            "no_serie" => "Hola"
        ];

        $rules = [
            "serie" => "required"
        ];
        $this->beConstructedWith($data, $rules);
        $this->shouldThrow('\JorgeAndrade\Exceptions\CfdiException')->duringMake();
    }

    function it_should_make_pass_data_if_enum_field_is_present()
    {
        $data = [
            "tipoDeComprobante" => "egreso"
        ];

        $rules = [
            "tipoDeComprobante" => "required|enum:egreso,ingreso"
        ];
        $this->beConstructedWith($data, $rules);
        $this->make()->shouldReturn(true);
    }
    function it_should_throw_an_exception_if_enum_field_isnt_present()
    {
        $data = [
            "tipoDeComprobante" => "retencion"
        ];

        $rules = [
            "tipoDeComprobante" => "required|enum:egreso,ingreso,traslado"
        ];
        $this->beConstructedWith($data, $rules);
        $this->shouldThrow('\JorgeAndrade\Exceptions\CfdiException')->duringMake();
    }

    function it_should_throw_a_bad_method_call_exception_if_method_not_exist()
    {
        $data = [
            "tipoDeComprobante" => "retencion"
        ];

        $rules = [
            "tipoDeComprobante" => "max:3"
        ];
        $this->beConstructedWith($data, $rules);
        $this->shouldThrow('\BadMethodCallException')->duringMake();
    }
}

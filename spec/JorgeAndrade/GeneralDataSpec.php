<?php namespace spec\JorgeAndrade;

use PhpSpec\ObjectBehavior;

class GeneralDataSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(['_serie' => 'DG']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('JorgeAndrade\GeneralData');
    }

}

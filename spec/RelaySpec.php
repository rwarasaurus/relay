<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Relay\Relay;

class RelaySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Relay::class);
    }
}

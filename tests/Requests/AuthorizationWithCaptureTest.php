<?php

namespace Tests\Requests;

use Tests\UnitTestCase;
use Mockery as m;
use Tests\Helpers\Authorization as AuthorizationHelper;

class AuthorizationWithCaptureTest extends UnitTestCase 
{
    use AuthorizationHelper;

    public function setUp()
    {
        parent::setUp();
        $this->setUpMocks();
    }

    /** @test */
    public function getParameters_it_returns_valid_capture_parameters()
    {
        $this->ignoreMissingMethods();
        $parameters = $this->request->getParameters();
        $this->assertSame('N', $parameters['PBX_AUTOSEULE']);
    }
}
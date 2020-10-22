<?php

namespace Tests\Responses;

use Bnb\PayboxGateway\ResponseCode;
use Bnb\PayboxGateway\ResponseField;
use Bnb\PayboxGateway\Responses\Exceptions\InvalidSignature;
use Bnb\PayboxGateway\Responses\Paybox\Verify;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\SignatureVerifier;
use Illuminate\Http\Request;
use Mockery as m;
use Tests\UnitTestCase;

class VerifyTest extends UnitTestCase
{
  protected $request;
  protected $signatureVerifier;
  protected $amountService;
  protected $verify;

  public function setUp(): void
  {
    parent::setUp();
    $this->request = m::mock(Request::class);
    $this->signatureVerifier = m::mock(SignatureVerifier::class);
    $this->amountService = m::mock(Amount::class);
    $this->verify = m::mock(Verify::class, [
      $this->request,
      $this->signatureVerifier,
      $this->amountService,
    ])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();
  }

  /** @test */
  public function isSuccess_it_throws_exception_when_signature_is_invalid()
  {
    $amount = 23.32;
    $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
    $signature = 'sampleSignature';

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once()
      ->passthru();
    $this->request
      ->shouldReceive('input')
      ->with('signature')
      ->once()
      ->andReturn($signature);
    $this->request
      ->shouldReceive('except')
      ->with('signature')
      ->once()
      ->andReturn($parameters);
    $this->signatureVerifier
      ->shouldReceive('isCorrect')
      ->with($signature, $parameters)
      ->once()
      ->andReturn(false);

    $this->expectException(InvalidSignature::class);
    $this->verify->isSuccess($amount);
  }

  /** @test */
  public function isSuccess_it_returns_true_when_all_conditions_are_met()
  {
    $amount = 23.32;
    $expectedAmount = 2332;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('amount')
      ->once()
      ->andReturn($expectedAmount);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertTrue($result);
  }

  /** @test */
  public function isSuccess_it_returns_false_when_no_authorization_number()
  {
    $amount = 23.32;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn(null);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /** @test */
  public function isSuccess_it_returns_false_when_response_code_is_different()
  {
    $amount = 23.32;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::INVALID_EXPIRATION_DATE);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /** @test */
  public function isSuccess_it_returns_false_when_invalid_amount_given()
  {
    $amount = 23.32;
    $expectedAmount = 2332;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('amount')
      ->once()
      ->andReturn($expectedAmount - 1);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /** @test */
  public function setParametersMap_it_uses_valid_parameters_when_set()
  {
    $amount = 23.32;
    $expectedAmount = 2332;
    $parameters = ['foo' => 'bar'];
    $signature = 'sampleSignature';

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once()
      ->passthru();
    $this->request
      ->shouldReceive('input')
      ->with('sig')
      ->once()
      ->andReturn($signature);
    $this->request
      ->shouldReceive('except')
      ->with('sig')
      ->once()
      ->andReturn($parameters);
    $this->signatureVerifier
      ->shouldReceive('isCorrect')
      ->with($signature, $parameters)
      ->once()
      ->andReturn(true);
    $this->request
      ->shouldReceive('input')
      ->with('nr')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('money')
      ->once()
      ->andReturn($expectedAmount);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertTrue($result);
  }

  /** @test */
  public function setParametersMap_it_throws_exception_when_no_amount_field_given()
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Amount is missing');

    $this->verify->setParametersMap([
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /** @test */
  public function setParametersMap_it_throws_exception_when_no_authorization_number_given()
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Authorization number is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /** @test */
  public function setParametersMap_it_throws_exception_when_no_response_code_given()
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Response code is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /** @test */
  public function setParametersMap_it_throws_exception_when_no_signature_given()
  {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Signature is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
    ]);
  }

  /** @test */
  public function getResponseCode_it_gets_valid_response_code()
  {
    $responseCode = 123123;
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->andReturn($responseCode);
    $result = $this->verify->getResponseCode();
    $this->assertSame('123123', $result);
  }
}

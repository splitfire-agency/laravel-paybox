<?php

namespace Tests\Services;

use Sf\PayboxGateway\Services\Billing;
use Tests\UnitTestCase;

/**
 * Class BillingTest
 * @package Tests\Services
 * @group BillingTest
 */
class BillingTest extends UnitTestCase
{
  /**
   * Test valid billing export xml
   */
  public function testValidBillingExportXml()
  {
    $service = new Billing();
    $service->setFirstName("John");
    $service->setLastName("Doe");
    $service->setAddress1("1 rue de la paix");
    $service->setZipCode("75000");
    $service->setCity("Paris");
    $service->setCountryCode(250);
    $this->assertSame(
      '<?xml version="1.0" encoding="utf-8"?><Billing><Address><FirstName>John</FirstName><LastName>Doe</LastName><Address1>1 rue de la paix</Address1><ZipCode>75000</ZipCode><City>Paris</City><CountryCode>250</CountryCode></Address></Billing>',
      $service->getXml()
    );
  }

  /**
   * Test valid billing export xml
   */
  public function testBillingExportXmlWithSpecialChar()
  {
    $service = new Billing();
    $service->setFirstName("John<");
    $service->setLastName("Doe>");
    $service->setAddress1("1 & rue d'la paix");
    $service->setZipCode("75000");
    $service->setCity("Paris");
    $service->setCountryCode(250);
    $this->assertSame(
      '<?xml version="1.0" encoding="utf-8"?><Billing><Address><FirstName>John&lt;</FirstName><LastName>Doe&gt;</LastName><Address1>1 &amp; rue d\'la paix</Address1><ZipCode>75000</ZipCode><City>Paris</City><CountryCode>250</CountryCode></Address></Billing>',
      $service->getXml()
    );
  }

  /**
   * Test empty billing export xml
   */
  public function testBillingExportXmlWithoutData()
  {
    $service = new Billing();
    $this->assertSame(
      '<?xml version="1.0" encoding="utf-8"?><Billing><Address><FirstName>xxx</FirstName><LastName>xxx</LastName><Address1>xxx</Address1><ZipCode>xxx</ZipCode><City>xxx</City><CountryCode>250</CountryCode></Address></Billing>',
      $service->getXml()
    );
  }

  /**
   * Test setCountryCodeFromAlpha2
   */
  public function testSetCountryCodeFromAlpha2()
  {
    $service = new Billing();
    $service->setCountryCodeFromAlpha2("IE");
    $this->assertSame(372, $service->getCountryCode());
    $service->setCountryCodeFromAlpha2("GR");
    $this->assertSame(300, $service->getCountryCode());
  }

  /**
   * Test setCountryCodeFromAlpha3
   */
  public function testSetCountryCode()
  {
    $service = new Billing();
    $service->setCountryCode("IRL");
    $this->assertSame(372, $service->getCountryCode());
    $service->setCountryCode("GR");
    $this->assertSame(300, $service->getCountryCode());
    $service->setCountryCode(833);
    $this->assertSame(833, $service->getCountryCode());
  }
}

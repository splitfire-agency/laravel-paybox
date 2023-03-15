<?php

namespace Sf\PayboxGateway\Requests\Paybox;

use Sf\PayboxGateway\Language;
use Sf\PayboxGateway\Requests\Request;
use Sf\PayboxGateway\ResponseField;
use Sf\PayboxGateway\Services\Amount;
use Sf\PayboxGateway\Services\Billing;
use Sf\PayboxGateway\Services\HmacHashGenerator;
use Sf\PayboxGateway\Services\ServerSelector;
use Carbon\Carbon;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;
use SimpleXMLElement;

abstract class Authorization extends Request
{
  /**
   * {@inheritdoc}
   */
  protected $type = "paybox";

  /**
   * Interface language.
   *
   * @var string
   */
  protected $language = Language::FRENCH;

  /**
   * @var string|null
   */
  protected $customerEmail = null;

  /**
   * @var float|null
   */
  protected $shoppingCartTotalPrice = null;

  /**
   * @var float|null
   */
  protected $shoppingCartTotalQuantity = null;

  /**
   * @var Billing|null
   */
  protected $billing = null;

  /**
   * @var array|null
   */
  protected $returnFields = null;

  /**
   * @var string|null
   */
  protected $customerPaymentAcceptedUrl = null;

  /**
   * @var string|null
   */
  protected $customerPaymentRefusedUrl = null;

  /**
   * @var string|null
   */
  protected $customerPaymentAbortedUrl = null;

  /**
   * @var string|null
   */
  protected $customerPaymentWaitingUrl = null;

  /**
   * @var string|null
   */
  protected $transactionVerifyUrl = null;

  /**
   * @var bool
   */
  protected $transactionCreateSubscriber = true;

  /**
   * @var HmacHashGenerator
   */
  protected $hmacHashGenerator;

  /**
   * @var Router
   */
  protected $router;

  /**
   * @var UrlGenerator
   */
  protected $urlGenerator;

  /**
   * Authorization constructor.
   *
   * @param ServerSelector $serverSelector
   * @param Config $config
   * @param HmacHashGenerator $hmacHashGenerator
   * @param UrlGenerator $urlGenerator
   * @param Amount $amountService
   */
  public function __construct(
    ServerSelector $serverSelector,
    Config $config,
    HmacHashGenerator $hmacHashGenerator,
    UrlGenerator $urlGenerator,
    Amount $amountService
  ) {
    parent::__construct($serverSelector, $config, $amountService);
    $this->hmacHashGenerator = $hmacHashGenerator;
    $this->urlGenerator = $urlGenerator;
  }

  /**
   * Get parameters that are required to make request.
   *
   * @return array
   */
  public function getParameters()
  {
    $params = $this->getBasicParameters();

    $params["PBX_HMAC"] = $this->hmacHashGenerator->get($params);

    return $params;
  }

  /**
   * Get basic parameters (all parameters except HMAC hash).
   *
   * @return array
   */
  protected function getBasicParameters()
  {
    $parameters = [
      "PBX_SITE" => $this->config->get("paybox.site"),
      "PBX_RANG" => $this->config->get("paybox.rank"),
      "PBX_IDENTIFIANT" => $this->config->get("paybox.id"),
      "PBX_TOTAL" => $this->amount,
      "PBX_DEVISE" => $this->currencyCode,
      "PBX_LANGUE" => $this->language,
      "PBX_CMD" => $this->paymentNumber,
      "PBX_HASH" => "SHA512",
      "PBX_PORTEUR" => $this->customerEmail,
      "PBX_RETOUR" => $this->getFormattedReturnFields(),
      "PBX_TIME" => $this->getFormattedDate($this->time ?: Carbon::now()),
      "PBX_EFFECTUE" => $this->getCustomerUrl(
        "customerPaymentAcceptedUrl",
        "accepted"
      ),
      "PBX_REFUSE" => $this->getCustomerUrl(
        "customerPaymentRefusedUrl",
        "refused"
      ),
      "PBX_ANNULE" => $this->getCustomerUrl(
        "customerPaymentAbortedUrl",
        "aborted"
      ),
      "PBX_ATTENTE" => $this->getCustomerUrl(
        "customerPaymentWaitingUrl",
        "waiting"
      ),
      "PBX_REPONDRE_A" => $this->getTransactionUrl(),
      "PBX_SHOPPINGCART" => $this->getShoppingCartXml(),
    ];
    if ($this->billing) {
      $parameters["PBX_BILLING"] = $this->billing->getXml();
    }

    return $parameters;
  }

  /**
   * Set shopping cart total price
   *
   * @param float $shoppingCartTotalPrice
   *
   * @return Authorization
   */
  public function setShoppingCartTotalPrice(float $shoppingCartTotalPrice)
  {
    $this->shoppingCartTotalPrice = $shoppingCartTotalPrice;

    return $this;
  }

  /**
   * Set shopping cart total quantity
   *
   * @param float $shoppingCartTotalQuantity
   *
   * @return Authorization
   */
  public function setShoppingCartTotalQuantity(float $shoppingCartTotalQuantity)
  {
    $this->shoppingCartTotalQuantity = $shoppingCartTotalQuantity;

    return $this;
  }

  /**
   * Get shopping cart xml from shopping cart infos
   * @return string
   */
  public function getShoppingCartXml()
  {
    $dom = new SimpleXMLElement(
      '<?xml version="1.0" encoding="utf-8"?><shoppingcart><total></total></shoppingcart>'
    );
    if ($this->shoppingCartTotalPrice) {
      $dom->total->addChild("totalPrice", $this->shoppingCartTotalPrice);
    }
    $totalQuantity = $this->shoppingCartTotalQuantity;
    if (!$totalQuantity) {
      $totalQuantity = 1;
    } elseif ($totalQuantity < 1) {
      $totalQuantity = 1;
    } elseif ($totalQuantity > 99) {
      $totalQuantity = 99;
    }
    $dom->total->addChild("totalQuantity", $totalQuantity);

    return $dom->asXML();
  }

  /**
   * Set billings infos
   *
   * @param Billing $billing
   *
   * @return Authorization
   */
  public function setBilling(Billing $billing)
  {
    $this->billing = $billing;

    return $this;
  }

  /**
   * Set interface language.
   *
   * @param string $language
   *
   * @return $this
   */
  public function setLanguage($language)
  {
    $this->language = $language;

    return $this;
  }

  /**
   * Set customer e-mail.
   *
   * @param string $email
   *
   * @return $this
   */
  public function setCustomerEmail($email)
  {
    $this->customerEmail = $email;

    return $this;
  }

  /**
   * Get formatted date in format required by Paybox.
   *
   * @param Carbon $date
   *
   * @return string
   */
  protected function getFormattedDate(Carbon $date)
  {
    return $date->format("c");
  }

  /**
   * Set return fields that will be when Paybox redirects back to website.
   *
   * @param array $returnFields
   *
   * @return $this
   */
  public function setReturnFields(array $returnFields)
  {
    $this->returnFields = $returnFields;

    return $this;
  }

  /**
   * Get return fields formatted in valid way.
   *
   * @return string
   */
  protected function getFormattedReturnFields()
  {
    $returnFields =
      (array) ($this->returnFields ?:
      $this->config->get("paybox.return_fields"));
    return collect($returnFields)
      ->reject(function ($value) {
        return !$this->transactionCreateSubscriber &&
          strtolower($value) ==
            strtolower(
              ResponseField::SUBSCRIPTION_CARD_OR_PAYPAL_AUTHORIZATION
            );
      })
      ->map(function ($value, $key) {
        return $key . ":" . $value;
      })
      ->implode(";");
  }

  /**
   * Set back url for customer when payment is accepted.
   *
   * @param string $url
   *
   * @return $this
   */
  public function setCustomerPaymentAcceptedUrl($url)
  {
    $this->customerPaymentAcceptedUrl = $url;

    return $this;
  }

  /**
   * Set back url for customer when payment is refused.
   *
   * @param string $url
   *
   * @return $this
   */
  public function setCustomerPaymentRefusedUrl($url)
  {
    $this->customerPaymentRefusedUrl = $url;

    return $this;
  }

  /**
   * Set back url for customer when payment is aborted.
   *
   * @param string $url
   *
   * @return $this
   */
  public function setCustomerPaymentAbortedUrl($url)
  {
    $this->customerPaymentAbortedUrl = $url;

    return $this;
  }

  /**
   * Set back url for customer when payment is waiting.
   *
   * @param string $url
   *
   * @return $this
   */
  public function setCustomerPaymentWaitingUrl($url)
  {
    $this->customerPaymentWaitingUrl = $url;

    return $this;
  }

  /**
   * Set url for transaction verification.
   *
   * @param string $url
   *
   * @return $this
   */
  public function setTransactionVerifyUrl($url)
  {
    $this->transactionVerifyUrl = $url;

    return $this;
  }

  /**
   * Set transaction create subscriber.
   *
   * @param $transactionCreateSubscriber
   *
   * @return $this
   */
  public function setTransactionCreateSubscriber($transactionCreateSubscriber)
  {
    $this->transactionCreateSubscriber = $transactionCreateSubscriber;

    return $this;
  }

  /**
   * Get customer url.
   *
   * @param string $variableName
   * @param string $configKey
   *
   * @return string
   */
  protected function getCustomerUrl($variableName, $configKey)
  {
    return $this->$variableName ?:
      $this->urlGenerator->route(
        $this->config->get("paybox.customer_return_routes_names." . $configKey)
      );
  }

  /**
   * Get transaction url.
   *
   * @return string
   */
  protected function getTransactionUrl()
  {
    return $this->transactionVerifyUrl ?:
      $this->urlGenerator->route(
        $this->config->get("paybox.transaction_verify_route_name")
      );
  }
}

<?php

namespace Drupal\commerce_payu;

use Drupal;
use Drupal\Core\Url;
use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;


class PayU
{
  private $baseUrl = 'https://staging.payu.co.za';

  private $apiVersion = 'ONE_ZERO';

  private $trace = true;
  private $exception = true;

  private $safeKey;
  private $client;

  private $production;

  /**
   * PayU constructor.
   *
   * @param $soapUsername
   * @param $soapPassword
   * @param $safeKey
   * @param bool $production
   */
  public function __construct($soapUsername, $soapPassword, $safeKey, $production = false)
  {
    $headerXml = <<<EOT
<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
<wsse:UsernameToken wsu:Id="UsernameToken-9" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
<wsse:Username>$soapUsername</wsse:Username>
<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">$soapPassword</wsse:Password>
</wsse:UsernameToken>
</wsse:Security>
EOT;

    $headerBody = new SoapVar($headerXml, XSD_ANYXML, null, null, null);
    $header = new SOAPHeader(
      'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
      'Security',
      $headerBody,
      true
    );

    $this->production = $production;

    if ($this->production) {
      $this->baseUrl = 'https://secure.payu.co.za';
    }

    try {
      $soapClient = new SoapClient(
        $this->baseUrl . "/service/PayUAPI?wsdl",
        [
          "trace" => intval($this->trace),
          "exception" => intval($this->exception),
        ]
      );
    } catch (SoapFault $e) {
      return;
    }

    $soapClient->__setSoapHeaders($header);

    $this->client = $soapClient;
    $this->safeKey = $safeKey;
  }

  /**
   *
   */
  public function setTransaction($reference, $basket, $cancelUrl, $returnUrl)
  {
    $payload = [
      'Api' => $this->apiVersion,
      'Safekey' => $this->safeKey,
      'TransactionType' => 'PAYMENT',
      'AdditionalInformation' => [
        'merchantReference' => $reference,
        'cancelUrl' => $cancelUrl,
        'returnUrl' => $returnUrl,
        'supportedPaymentMethods' => 'CREDITCARD, EFT_PRO',
      ],
      'Basket' => $basket
    ];

    try {
      $result = $this->client->setTransaction($payload);
    } catch (SoapFault $e) {
      \Drupal::logger('payu')->error($e->getMessage());
    }

    $data = json_decode(json_encode($result), true);

    $success = (isset($data['return']['successful'])) ? ($data['return']['successful'] === true) : false;
    $reference = (isset($data['return']['payUReference'])) ? $data['return']['payUReference'] : null;

    return [
      "success" => $success,
      "reference" => $reference,
      "redirect_url" => $this->baseUrl . '/rpp.do?PayUReference=' . $reference
    ];
  }

  public function getTransaction($reference)
  {
    $payload = [
      'Api' => $this->apiVersion,
      'Safekey' => $this->safeKey,
      'AdditionalInformation' => [
        'payUReference' => $reference
      ],
    ];

    $soapCallResult = $this->client->getTransaction($payload);

    $result = json_decode(json_encode($soapCallResult), true);

    if (!array_key_exists("return", $result)) {
      return false;
    }

    return $result["return"];
  }
}

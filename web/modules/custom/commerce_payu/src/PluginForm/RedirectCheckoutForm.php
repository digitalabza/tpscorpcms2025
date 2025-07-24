<?php

namespace Drupal\commerce_payu\PluginForm;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;

use Drupal;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_payment\Exception\DeclineException;

use Drupal\commerce_payu\CurrencyCalculator;
use Drupal\commerce_payu\Plugin\Commerce\PaymentGateway\RedirectCheckout;
use Drupal\commerce_payu\PayU;


class RedirectCheckoutForm extends BasePaymentOffsiteForm implements ContainerInjectionInterface
{
  /**
   * @var CurrencyCalculator
   */

  protected $currency_calculator;
  function __construct(CurrencyCalculator $currency_calculator)
  {
    $this->currency_calculator = $currency_calculator;
  }

  public static function create(ContainerInterface $container)
  {
      return new static(
      $container->get('commerce_payu.currency_calculator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $configuration = $this->getConfiguration();

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    $payment->save();

      $payu = new PayU(
      $configuration["soap_username"],
      $configuration["soap_password"],
      $configuration["safe_key"],
      $configuration["production"]
    );

    $orderId = $payment->getOrderId();
    $order = $payment->getOrder();

    $price = $order->getTotalPrice();

    $basket = [
      "description" => $order->getOrderNumber(),
      "amountInCents" => (string)(int)($price->getNumber() * 100),
      "currencyCode" => $price->getCurrencyCode(),
    ];

      $cancelUrl = Url::fromRoute(
      'commerce_payment.checkout.cancel',
      ['commerce_order' => $orderId, 'step' => 'payment',],
      ['absolute' => TRUE]
    )->toString();

      $returnUrl = Url::fromRoute(
      'commerce_payment.checkout.return',
      ['commerce_order' => $orderId, 'step' => 'payment',],
      ['absolute' => TRUE]
    )->toString();

    $result = $payu->setTransaction($orderId, $basket, $cancelUrl, $returnUrl);
    $success = $result["success"];
    $reference = $result["reference"];
    $redirect_url = $result["redirect_url"];

      if (!$success) {
      // drupal_set_message('Payment was not successful');
      \Drupal::messenger()->addMessage('Payment was not successful');
      throw new DeclineException();
      return false;
    }

    $order->setData('payu_reference', $result["reference"]);
    $order->save();

      if ($success && $reference) {
      // redirect to PayU
      header('Location: ' . $redirect_url);
      die();
    }

      return $this->buildRedirectForm(
      $form,
      $form_state,
      $redirect_url,
      ["foo" => 1],
      PaymentOffsiteForm::REDIRECT_GET
    );

    // return $this->buildRedirectForm( $form, $form_state, $url, $data, 'post' );

  }


  /**
   * @return array
   */
  private function getConfiguration()
  {
    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    return $payment_gateway_plugin->getConfiguration();
  }
}

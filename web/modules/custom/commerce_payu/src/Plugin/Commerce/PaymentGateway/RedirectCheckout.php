<?php

namespace Drupal\commerce_payu\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Exception\DeclineException;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\commerce_payu\PayU;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Provides the PayU offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "payu_checkout",
 *   label = @Translation("PayU (Redirect to payu)"),
 *   display_label = @Translation("PayU"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_payu\PluginForm\RedirectCheckoutForm",
 *   },
 * )
 */
class RedirectCheckout extends OffsitePaymentGatewayBase {
    /**
     * {@inheritdoc}
     */
  public function defaultConfiguration()
  {
    return [
      'soap_username' => '',
      'soap_password' => '',
      'safe_key' => '',
      'production' => false,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['soap_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('SOAP Username'),
      '#description' => $this->t('This is the SOAP Username for PayU.'),
      '#default_value' => $this->configuration['soap_username'],
      '#required' => TRUE,
    ];

    $form['soap_password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('SOAP Password'),
      '#description' => $this->t('This is the SOAP Password for PayU.'),
      '#default_value' => $this->configuration['soap_password'],
      '#required' => TRUE,
    ];

    $form['safe_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Safe key'),
      '#description' => $this->t('The Safe key for PayU.'),
      '#default_value' => $this->configuration['safe_key'],
      '#required' => TRUE,
    ];

    $form['production'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Production'),
      '#description' => $this->t('If set, the payment integration will call live PayU endpoints.'),
      '#default_value' => $this->configuration['production'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['soap_username'] = $values['soap_username'];
      $this->configuration['soap_password'] = $values['soap_password'];
      $this->configuration['safe_key'] = $values['safe_key'];
      $this->configuration['production'] = $values['production'];
    }
  }

  public function onReturn(OrderInterface $order, Request $request)
  {
    $reference = $request->query->get("PayUReference");

    $payu = new PayU(
      $this->configuration["soap_username"],
      $this->configuration["soap_password"],
      $this->configuration["safe_key"],
      $this->configuration["production"]
    );

    $result = $payu->getTransaction($reference);

    if ($result == 'SUCCESSFUL') {

      $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
      $payment = $payment_storage->create([
        // 'state' => $result["successful"] ? 'Completed' : 'Failed',
        'state' => 'completed',
        'amount' => $order->getTotalPrice(),
        'payment_gateway' => "payu_checkout",
        'order_id' => $result["merchantReference"],
        'remote_id' => $result["payUReference"],
        'remote_state' =>  $result["transactionState"],
      ]);

      // $state = ($result["successful"]) ? 'completed' : 'failed';
      // // $payment->setState($state)->save();
      // $payment->setState($state);

      // $logger->info('Saving Payment information. Transaction reference: ' . $merchantTransactionReference);

      $payment->save();

      // drupal_set_message('Payment was processed');
    \Drupal::messenger()->addMessage('Payment was processed. Thank you for placing your order');

    }

    // $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    // $payment = $payment_storage->create([
    //   'state' => $result["successful"] ? 'Completed' : 'Failed',
    //   'amount' => $order->getTotalPrice(),
    //   'payment_gateway' => "payu_checkout",
    //   'order_id' => $result["merchantReference"],
    //   'remote_id' => $result["payUReference"],
    //   'remote_state' =>  $result["transactionState"],
    // ]);

    // $state = ($result["successful"]) ? 'completed' : 'failed';
    // // $payment->setState($state)->save();
    // $payment->setState($state);

    // // $logger->info('Saving Payment information. Transaction reference: ' . $merchantTransactionReference);

    // $payment->save();
    // $this->messenger()->addStatus('Payment was processed');

    // else if (!$result["successful"]) {
      elseif (!$result == 'SUCCESSFUL')  {

      // drupal_set_message('Payment was not processed');
      \Drupal::messenger()->addMessage('Payment was not processed');
      throw new DeclineException();
    }

  }
}

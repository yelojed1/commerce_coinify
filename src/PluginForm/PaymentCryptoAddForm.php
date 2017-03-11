<?php

namespace Drupal\commerce_crypto\PluginForm;

use Drupal\commerce_crypto\PluginForm\PaymentCryptoAddForm as BasePaymentCryptoAddForm;
use Drupal\Core\Form\FormStateInterface;

class PaymentCryptoForm extends BasePaymentCryptoAddForm {

  /**
   * {@inheritdoc}
   */
  protected function buildBitcoinPaymentForm(array $element, FormStateInterface $form_state) {
    $element = parent::buildBitcoinPaymentForm($element, $form_state);
    // Default to a known valid test credit card number.
    $element['number']['#default_value'] = '4111111111111111';

    return $element;
  }

}

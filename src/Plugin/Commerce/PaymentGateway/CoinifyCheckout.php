<?php

namespace Drupal\commerce_coinify\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\Exception\InvalidRequestException;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\commerce_price\Price;
use Drupal\commerce_price\RounderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the coinify Currency payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "coinify_currency_checkout",
 *   label = @Translation("coinify Currency (Express Checkout)"),
 *   display_label = @Translation("coinify Currency"),
 *    forms = {
 *     "add-payment-method" = "Drupal\commerce_coinify\PluginForm\CoinifyCheckoutForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 * )
 */
class CoinifyCheckout extends OffsitePaymentGatewayBase implements CoinifyCheckoutInterface {

 /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The rounder.
   *
   * @var \Drupal\commerce_price\RounderInterface
   */
  protected $rounder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PaymentTypeManager $payment_type_manager, PaymentMethodTypeManager $payment_method_type_manager, ClientInterface $client, RounderInterface $rounder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $payment_type_manager, $payment_method_type_manager);
    $this->httpClient = $client;
    $this->rounder = $rounder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.commerce_payment_type'),
      $container->get('plugin.manager.commerce_payment_method_type'),
      $container->get('http_client'),
      $container->get('commerce_price.rounder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'api_username' => '',
      'api_password' => '',
      'signature' => '',
      'solution_type' => 'Mark',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $this->configuration['api_key'],
      '#required' => TRUE,
    ];
    $form['api_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Secret'),
      '#default_value' => $this->configuration['api_secret'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
   /* parent::validateConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      if (!empty($values['api_key']) && !empty($values['api_secret'])) {
        $this->configuration['api_key'] = $values['api_key'];
        $this->configuration['api_secret'] = $values['api_secret'];

        // Make a Coinify request to validate the Coinify API credentials.
        $response = $this->doRequest([
          'METHOD' => 'GetBalance',
        ]);

        if ($response['ACK'] != 'Success') {
          $form_state->setError($form['api_key'], $this->t('Invalid API credentials.'));
        }
      }
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['api_key'] = $values['api_key'];
      $this->configuration['api_secret'] = $values['api_secret'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
 
  }

  /**
   * {@inheritdoc}
   */
  public function capturePayment(PaymentInterface $payment, Price $amount = NULL) {
 
  }

  /**
   * {@inheritdoc}
   */
  public function voidPayment(PaymentInterface $payment) {

  }

  /**
   * {@inheritdoc}
   */
  public function refundPayment(PaymentInterface $payment, Price $amount = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    if ($this->getMode() == 'test') {
      return 'https://api.sandbox.coinify.com/v3';
    }
    else {
      return 'https://api.coinify.com/v3';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setExpressCheckout(PaymentInterface $payment, array $extra) {
    $order = $payment->getOrder();

    $amount = $this->rounder->round($payment->getAmount());
    $configuration = $this->getConfiguration();
    //TODO Code the request.

  }

  /**
   * {@inheritdoc}
   */
  public function doVoid(PaymentInterface $payment) {

  }

  /**
   * {@inheritdoc}
   */
  public function doRefundTransaction(PaymentInterface $payment, array $extra) {

  }

  /**
   * {@inheritdoc}
   */
  public function doRequest(array $nvp_data) {
    // Add the default name-value pairs to the array.
    /*$configuration = $this->getConfiguration();
    $nvp_data += [
      // API credentials.
      'USER' => $configuration['api_username'],
      'PWD' => $configuration['api_password'],
      'SIGNATURE' => $configuration['signature'],
      'VERSION' => '124.0',
    ];

    $mode = $this->getMode();
    if ($mode == 'test') {
      $url = 'https://api-3t.sandbox.paypal.com/nvp';
    }
    else {
      $url = 'https://api-3t.paypal.com/nvp';
    }

    // Make PayPal request.
    $request = $this->httpClient->post($url, [
      'form_params' => $nvp_data,
    ])->getBody()
      ->getContents();

    parse_str(html_entity_decode($request), $paypal_response);

    return $paypal_response;*/
  }

}

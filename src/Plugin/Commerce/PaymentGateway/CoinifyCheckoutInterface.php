<?php

namespace Drupal\commerce_coinify\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\SupportsAuthorizationsInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\SupportsRefundsInterface;

/**
 * Provides the interface for the Coinify Checkout payment gateway.
 */
interface CoinifyCheckoutInterface extends OffsitePaymentGatewayInterface, SupportsAuthorizationsInterface, SupportsRefundsInterface {

}

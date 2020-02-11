<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Glue\Checkout\RestApi;

use Codeception\Util\HttpCode;
use Generated\Shared\DataBuilder\AddressBuilder;
use Generated\Shared\Transfer\RestCheckoutErrorTransfer;
use Generated\Shared\Transfer\RestPaymentTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use PyzTest\Glue\Checkout\CheckoutApiTester;
use PyzTest\Glue\Checkout\RestApi\Fixtures\GuestCheckoutRestApiFixtures;
use Spryker\Glue\CheckoutRestApi\CheckoutRestApiConfig;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Glue
 * @group Checkout
 * @group RestApi
 * @group GuestCheckoutRestApiCest
 * Add your own group annotations below this line
 * @group EndToEnd
 */
class GuestCheckoutRestApiCest
{
    protected const HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID = 'X-Anonymous-Customer-Unique-Id';

    protected const RESPONSE_CODE_CART_IS_EMPTY = '1104';
    protected const RESPONSE_DETAILS_CART_IS_EMPTY = 'Cart is empty.';

    /**
     * @var \PyzTest\Glue\Checkout\RestApi\Fixtures\GuestCheckoutRestApiFixtures
     */
    protected $fixtures;

    /**
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return void
     */
    public function loadFixtures(CheckoutApiTester $I): void
    {
        $this->fixtures = $I->loadFixtures(GuestCheckoutRestApiFixtures::class);
    }

    /**
     * @depends loadFixtures
     *
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return void
     */
    public function requestWithNoItemsInQuote(CheckoutApiTester $I): void
    {
        // Arrange
        $customerTransfer = $this->fixtures->getGuestCustomerTransfer();
        $I->haveHttpHeader(
            static::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID,
            $this->fixtures->getGuestCustomerReference()
        );

        $quoteTransfer = $I->havePersistentQuoteWithItems($customerTransfer, []);
        $shippingAddressTransfer = (new AddressBuilder())->build();

        $url = $I->buildCheckoutUrl();
        $urlParams = [
            'data' => [
                'type' => CheckoutRestApiConfig::RESOURCE_CHECKOUT,
                'attributes' => [
                    'idCart' => $quoteTransfer->getUuid(),
                    'billingAddress' => $I->getAddressRequestParams($quoteTransfer->getBillingAddress()),
                    'shippingAddress' => $I->getAddressRequestParams($shippingAddressTransfer),
                    'customer' => $I->getCustomerRequestParams($customerTransfer),
                    'payments' => [
                        [
                            RestPaymentTransfer::PAYMENT_METHOD_NAME => 'invoice',
                            RestPaymentTransfer::PAYMENT_PROVIDER_NAME => 'DummyPayment',
                            RestPaymentTransfer::PAYMENT_SELECTION => 'dummyPaymentInvoice',
                            RestPaymentTransfer::DUMMY_PAYMENT_INVOICE => [
                                'dateOfBirth' => '08.04.1986',
                            ],
                            'amount' => 899910,
                        ],
                    ],
                    'shipment' => [
                        ShipmentMethodTransfer::ID_SHIPMENT_METHOD => 1,
                        [
                            ShipmentMethodTransfer::ID => 1,
                            ShipmentMethodTransfer::CARRIER_NAME => 'Spryker Dummy Shipment',
                            ShipmentMethodTransfer::NAME => 'Standard',
                            ShipmentMethodTransfer::TAX_RATE => null,
                            'price' => 490,
                            'shipmentDeliveryTime' => null,
                        ],
                    ],
                ],
            ],
        ];

        // Act
        $I->sendPOST($url, $urlParams);

        // Assert
        $I->assertResponseHasCorrectInfrastructure(HttpCode::UNPROCESSABLE_ENTITY);

        $errors = $I->amSure('I\'m taking the error info from the returned resource')
            ->whenI()
            ->grabDataFromResponseByJsonPath('$.errors[0]');
        $I->assertEquals($errors[RestCheckoutErrorTransfer::CODE], static::RESPONSE_CODE_CART_IS_EMPTY);
        $I->assertEquals($errors[RestCheckoutErrorTransfer::STATUS], HttpCode::UNPROCESSABLE_ENTITY);
        $I->assertEquals($errors[RestCheckoutErrorTransfer::DETAIL], static::RESPONSE_DETAILS_CART_IS_EMPTY);
    }

    /**
     * @depends loadFixtures
     *
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return void
     */
    public function requestWithOneItemInQuoteAndInvoicePayment(CheckoutApiTester $I): void
    {
        // Arrange
        $customerTransfer = $this->fixtures->getGuestCustomerTransfer();
        $I->haveHttpHeader(
            static::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID,
            $this->fixtures->getGuestCustomerReference()
        );

        $quoteTransfer = $I->havePersistentQuoteWithItems($customerTransfer, [$this->fixtures->getProductConcreteTransfer()]);
        $shippingAddressTransfer = $quoteTransfer->getItems()[0]->getShipment()->getShippingAddress();

        $url = $I->buildCheckoutUrl();
        $urlParams = [
            'data' => [
                'type' => CheckoutRestApiConfig::RESOURCE_CHECKOUT,
                'attributes' => [
                    'idCart' => $quoteTransfer->getUuid(),
                    'billingAddress' => $I->getAddressRequestParams($quoteTransfer->getBillingAddress()),
                    'shippingAddress' => $I->getAddressRequestParams($shippingAddressTransfer),
                    'customer' => $I->getCustomerRequestParams($customerTransfer),
                    'payments' => $I->getPaymentRequestParams(),
                    'shipment' => $I->getShipmentRequestParams(),
                ],
            ],
        ];

        // Act
        $I->sendPOST($url, $urlParams);

        // Assert
        $I->assertResponseHasCorrectInfrastructure();

        $I->amSure('The returned resource is of correct type')
            ->whenI()
            ->seeResponseDataContainsSingleResourceOfType(CheckoutRestApiConfig::RESOURCE_CHECKOUT);

        $I->assertCheckoutResponseResourceHasCorrectData();

        $I->amSure('The returned resource has correct self link')
            ->whenI()
            ->seeSingleResourceHasSelfLink($I->buildCheckoutUrl());
    }

    /**
     * @depends loadFixtures
     *
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return void
     */
    public function requestWithOneItemInQuoteAndCreditCardPayment(CheckoutApiTester $I): void
    {
        // Arrange
        $customerTransfer = $this->fixtures->getGuestCustomerTransfer();
        $I->haveHttpHeader(
            static::HEADER_ANONYMOUS_CUSTOMER_UNIQUE_ID,
            $this->fixtures->getGuestCustomerReference()
        );

        $quoteTransfer = $I->havePersistentQuoteWithItems($customerTransfer, [$this->fixtures->getProductConcreteTransfer()]);
        $shippingAddressTransfer = $quoteTransfer->getItems()[0]->getShipment()->getShippingAddress();

        $url = $I->buildCheckoutUrl();
        $urlParams = [
            'data' => [
                'type' => CheckoutRestApiConfig::RESOURCE_CHECKOUT,
                'attributes' => [
                    'idCart' => $quoteTransfer->getUuid(),
                    'billingAddress' => $I->getAddressRequestParams($quoteTransfer->getBillingAddress()),
                    'shippingAddress' => $I->getAddressRequestParams($shippingAddressTransfer),
                    'customer' => $I->getCustomerRequestParams($customerTransfer),
                    'payments' => $I->getPaymentRequestParams('credit card'),
                    'shipment' => $I->getShipmentRequestParams(),
                ],
            ],
        ];

        // Act
        $I->sendPOST($url, $urlParams);

        // Assert
        $I->assertResponseHasCorrectInfrastructure();

        $I->amSure('The returned resource is of correct type')
            ->whenI()
            ->seeResponseDataContainsSingleResourceOfType(CheckoutRestApiConfig::RESOURCE_CHECKOUT);

        $I->assertCheckoutResponseResourceHasCorrectData();

        $I->amSure('The returned resource has correct self link')
            ->whenI()
            ->seeSingleResourceHasSelfLink($I->buildCheckoutUrl());
    }
}

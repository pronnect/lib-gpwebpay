# pronnect/gpwebpay

[![Tests](https://github.com/pronnect/lib-gpwebpay/actions/workflows/tests.yml/badge.svg)](https://github.com/pronnect/lib-gpwebpay/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/pronnect/lib-gpwebpay/graph/badge.svg?token=XCC7UPOCEB)](https://codecov.io/gh/pronnect/lib-gpwebpay)

PHP library for communicating with the **GP Webpay** payment gateway.

Supports two independent integration modes:

| Mode | Transport | Namespace |
|---|---|---|
| **WS API v2.0** (SOAP) | SOAP/WSDL — server-to-server | `Pronnect\GpWebPay` |
| **HTTP API** (redirect) | Browser redirect (GET/POST form) | `Pronnect\GpWebPay\Http` |

Requires PHP 8.1+, `ext-openssl`. SOAP mode additionally needs `ext-soap` and `ext-dom`. PSR-3 logger is optional.

---

## Installation

```bash
composer require pronnect/lib-gpwebpay
```

---

## Architecture

The library follows a two-namespace design:

| Namespace | Location | Purpose |
|---|---|---|
| `Pronnect\GpWebPayApi` | `api/` | Public interfaces only — code against these |
| `Pronnect\GpWebPay` | `src/` | Concrete implementations |

### Core flow

1. Create a `Config` with endpoint, merchant certificates, and provider code.
2. Instantiate `Gateway` with the config, an optional `DigestSigner`, and an optional PSR-3 logger.
3. Construct a **Request** object and call the matching method on `Gateway`.
4. `Gateway` auto-fills `provider`, `merchantNumber`, `messageId` from config, signs the request, delegates to `WebService` (a `SoapClient` wrapper), verifies the response signature, and returns a typed **Response** object.

### Digest signing

Requests and responses implement `SignedInterface`. The digest is a `|`-separated string of specific fields in WSDL-defined order, signed with `openssl_sign` using the merchant private key, and verified with the GPE public key.

---

## Configuration

```php
use Pronnect\GpWebPay\Config;
use Pronnect\GpWebPay\ServiceProvider;

$config = new Config(
    wsUri: 'https://test.3dsecure.gpwebpay.com/pay-ws/v1/PaymentService',
    provider: ServiceProvider::CSOB,
    merchantNumber: '123456789',
    gpePublicKey: '/path/to/gpwebpay-pub.pem',
    merchantPrivateKey: '/path/to/merchant.key',
    merchantPrivateKeyPassword: 'secret',
);
```

**Test endpoint:** `https://test.3dsecure.gpwebpay.com/pay-ws/v1/PaymentService`
**Prod endpoint:** `https://3dsecure.gpwebpay.com/pay-ws/v1/PaymentService`

---

## Usage

```php
use Pronnect\GpWebPay\Gateway;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Request\PaymentLinkRequest;

$gateway = new Gateway($config);

// Create a payment link
$request = (new PaymentLinkRequest())
    ->setPaymentNumber('ORDER-001')
    ->setAmount(10000)          // in lowest currency unit (e.g. cents)
    ->setCurrencyCode(978)      // ISO 4217 — 978 = EUR
    ->setCaptureFlag(true)
    ->setUrl('https://example.com/return');

$response = $gateway->createPaymentLink($request);
echo $response->getPaymentLink(); // redirect the cardholder here
```

---

## Implemented API operations

| Gateway method | Request class | Response class | Description |
|---|---|---|---|
| `echo()` | — | — | Connectivity check |
| `createPaymentLink` | `PaymentLinkRequest` | `PaymentLinkResponse` | Create a hosted payment page link. Supports `registerToken` and `registerRecurring` flags |
| `getPaymentLinkStatus` | `PaymentLinkStatusRequest` | `StatusResponse` | Get payment link status |
| `revokePaymentLink` | `RevokePaymentLinkRequest` | `StateResponse` | Cancel a payment link |
| `getPaymentStatus` | `PaymentStatusRequest` | `StateResponse` | Get payment status (state, subStatus) |
| `getPaymentDetail` | `PaymentDetailRequest` | `PaymentDetailResponse` | Get full payment detail (card brand, amounts, timestamps, …) |
| `getTokenStatus` | `TokenStatusRequest` | `StatusResponse` | Get card token status |
| `processTokenRevoke` | `TokenRevokeRequest` | `StatusResponse` | Revoke a card token |
| `processCardOnFilePayment` | `CardOnFilePaymentRequest` | `CardOnFilePaymentResponse` | Server-side COF payment using a stored token (MIT). Soft decline (PRCODE=46) surfaces `authenticationLink` via `CardOnFilePaymentFaultDetail` |
| `getCardData` | `CardDataRequest` | `CardDataResponse` | Retrieve masked card data and card art image for a token or master payment |
| `processCapture` | `CaptureRequest` | `StateResponse` | Capture a pre-authorized amount (deferred capture flow) |
| `processCaptureReverse` | `CaptureReverseRequest` | `StateResponse` | Reverse a capture by captureNumber |
| `processAuthorizationReverse` | `AuthorizationReverseRequest` | `StateResponse` | Reverse a pre-authorization (void before capture) |
| `processPaymentClose` | `PaymentCloseRequest` | `StateResponse` | Close a payment (mark as final, no further changes) |
| `processPaymentDelete` | `PaymentDeleteRequest` | `StateResponse` | Delete a payment that has not been settled |
| `processRefund` | `RefundRequest` | `StateResponse` | Issue a partial or full refund for a settled payment |
| `processRefundReverse` | `RefundReverseRequest` | `StateResponse` | Reverse a previously issued refund by refundNumber |
| `getMasterPaymentStatus` | `MasterPaymentStatusRequest` | `StatusResponse` | Get status of a master (linked) payment group |
| `processMasterPaymentRevoke` | `MasterPaymentStatusRequest` | `StatusResponse` | Cancel a master (linked) payment group |
| `processRecurringPayment` | `RecurringPaymentRequest` | `RecurringPaymentResponse` | Recurring (MIT) payment using a master payment number |
| `processUsageBasedPayment` | `UsageBasedPaymentRequest` | `CardOnFilePaymentResponse` | Usage-based payment with stored token |
| `processUsageBasedSubscriptionPayment` | `UsageBasedSubscriptionPaymentRequest` | `RecurringPaymentResponse` | Usage-based subscription payment |
| `processRegularSubscriptionPayment` | `RegularSubscriptionPaymentRequest` | `RecurringPaymentResponse` | Regular subscription payment |
| `processPrepaidPayment` | `PrepaidPaymentRequest` | `RecurringPaymentResponse` | Prepaid card payment |
| `processBatchClose` | `BatchCloseRequest` | `BatchCloseResponse` | Close the settlement batch |
| `processPayout` | `PayoutRequest` | `RecurringPaymentResponse` | General payout (push-to-card) |
| `processPayoutWinning` | `PayoutWinningRequest` | `RecurringPaymentResponse` | Payout — winnings |
| `processPayoutInsurance` | `PayoutInsuranceRequest` | `RecurringPaymentResponse` | Payout — insurance |
| `getSubsqTransBatchStatus` | `SubsqTransBatchStatusRequest` | `SubsqTransBatchStatusResponse` | Get subsequent transactions batch status |
| `resolvePaymentStatus` | `ResolvePaymentStatusRequest` | `StateResponse` | Resolve push payment status notification |
| `mpsPreCheckout` | `MpsPreCheckoutRequest` | `MpsPreCheckoutResponse` | Masterpass pre-checkout |
| `mpsExpressCheckout` | `MpsExpressCheckoutRequest` | `RecurringPaymentResponse` | Masterpass express checkout |

### Token / Card-on-File flow

```php
// Step 1 — register a token during PaymentLink checkout
$request = (new PaymentLinkRequest())
    ->setPaymentNumber('ORDER-001')
    ->setAmount(10000)
    ->setCurrencyCode(978)
    ->setCaptureFlag(true)
    ->setUrl('https://example.com/return')
    ->setRegisterToken(true);   // request token registration

$link = $gateway->createPaymentLink($request);

// Step 2 — use the token for a server-side payment (no cardholder present)
use Pronnect\GpWebPay\Request\CardOnFilePaymentRequest;
use SoapFault;

$cofRequest = (new CardOnFilePaymentRequest())
    ->setPaymentNumber('ORDER-002')
    ->setAmount(5000)
    ->setCurrencyCode(978)
    ->setCaptureFlag(1)
    ->setTokenData($tokenReceivedFromGateway)
    ->setReturnUrl('https://example.com/return');

try {
    $response = $gateway->processCardOnFilePayment($cofRequest);
} catch (SoapFault $e) {
    // Soft decline — cardholder must authenticate (3DS)
    $authLink = $e->detail->cardOnFilePaymentFaultDetail->authenticationLink ?? null;
}
```

---

## HTTP API (redirect-based)

The HTTP API is a **browser-redirect** integration: your server builds a signed URL (or hidden POST form), redirects the cardholder to GP Webpay, and GP Webpay redirects back to your return URL with a signed callback.

### HTTP API configuration

```php
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Http\HttpConfig;
use Pronnect\GpWebPay\Http\HttpGateway;

$config = new HttpConfig([
    'isTestEnvironment'          => true,   // false for production
    'merchantNumber'             => '0123456789',
    'GPEPublicKey'               => file_get_contents('/path/to/gpe.pub.pem'),
    'merchantPrivateKey'         => file_get_contents('/path/to/merchant.key'),
    'merchantPrivateKeyPassword' => 'secret',
    'defaultLang'                => 'CS',   // optional; 2-letter ISO 639-1
]);

$rawSigner = new DigestSigner(
    $config->getGPEPublicKey(),
    $config->getMerchantPrivateKey(),
    $config->getMerchantPrivateKeyPassword(),
);

// Always use the factory — it wraps DigestSigner in Base64DigestSigner automatically
$gateway = HttpGateway::create($config, $rawSigner);
```

**Test endpoint:** `https://test.3dsecure.gpwebpay.com/pgw/order.do`
**Prod endpoint:** `https://3dsecure.gpwebpay.com/pgw/order.do`

### Redirect (GET)

```php
use Pronnect\GpWebPay\Http\Request\CardPaymentRequest;

$request = new CardPaymentRequest(
    orderNumber: 123456,
    amount:      19900,      // in lowest currency unit (e.g. hellers / cents)
    currency:    203,        // ISO 4217 — 203 = CZK
    depositFlag: 1,          // 1 = direct capture, 0 = pre-auth
    url:         'https://myshop.cz/return',
);
$request->setDescription('Order #123456');
$request->setLang('CS');

// Redirect cardholder to GP Webpay
header('Location: ' . $gateway->getRedirectUrl($request));
exit;
```

### POST form

When ADDINFO (3DS2 additional data) is present, the spec requires a hidden POST form instead of a GET redirect:

```php
use Pronnect\GpWebPay\Http\Request\AddInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo;

$addInfo = (new AddInfo())
    ->setCardholderInfo((new CardholderInfo())->setAddrMatch('Y'));

$request->setAddInfo($addInfo->toXml());

$params = $gateway->getFormParams($request); // same as getRedirectUrl but returns array
?>
<form method="post" action="<?= htmlspecialchars($gateway->getHttpUri()) ?>" id="gpwp">
  <?php foreach ($params as $k => $v): ?>
  <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
  <?php endforeach; ?>
</form>
<script>document.getElementById('gpwp').submit();</script>
```

### Processing the return URL callback

GP Webpay redirects back to your return URL with signed GET params. Use `HttpGateway::processCallback()` for the full flow (signature verification + response object):

```php
use Pronnect\GpWebPay\Http\Exception\InvalidCallbackException;
use Pronnect\GpWebPay\Http\Exception\InvalidDigestException;

try {
    $response = $gateway->processCallback($_GET);
    if ($response->isSuccess()) {
        // payment approved — fulfil the order
        echo $response->getOrderNumber(); // the orderNumber you set in the request
        echo $response->getPrCode();      // '0' = success
    }
} catch (InvalidCallbackException $e) {
    // DIGEST missing — not a GP Webpay callback
} catch (InvalidDigestException $e) {
    // Signature verification failed — reject
}
```

Or use the standalone `ReturnUrlVerifier` if you only need signature checking:

```php
use Pronnect\GpWebPay\ReturnUrlVerifier;

$verifier = new ReturnUrlVerifier(
    gpePublicKey:               $config->getGPEPublicKey(),
    merchantNumber:             $config->getMerchantNumber(),
    merchantPrivateKey:         $config->getMerchantPrivateKey(),
    merchantPrivateKeyPassword: $config->getMerchantPrivateKeyPassword(),
);

if ($verifier->verify($_GET)) {
    // DIGEST1 valid — callback is authentic
}
```

### HTTP API constants

```php
use Pronnect\GpWebPay\Http\Operation;      // 'CREATE_ORDER'
use Pronnect\GpWebPay\Http\PayMethod;      // 'CRD', 'MCM', 'CSH', …
use Pronnect\GpWebPay\Http\DepositFlag;    // IMMEDIATE = 1, PREAUTH = 0
use Pronnect\GpWebPay\Http\ReturnCode;     // OK = '0', DECLINED = '1', …
use Pronnect\GpWebPay\Http\UserParam1;     // RECURRING_PAYMENT, CARD_ON_FILE, …
```

### VRCODE encryption

```php
use Pronnect\GpWebPay\Http\VrCodeEncryptor;

// AES-128-CBC, 16-byte key from the bank, fixed zero IV, uppercase hex output
$encrypted = VrCodeEncryptor::encrypt('MY_VRCODE', $aes16ByteKey);
// 'B179802DFB94DE8AAA94D840CABBEC6A' (32 hex chars for ≤15 char input)
```

### HTTP API error handling

```php
use Pronnect\GpWebPay\Http\Exception\HttpRequestException;   // invalid request params
use Pronnect\GpWebPay\Http\Exception\InvalidDigestException;  // bad DIGEST/DIGEST1
use Pronnect\GpWebPay\Http\Exception\InvalidCallbackException; // DIGEST missing

try {
    $response = $gateway->processCallback($_GET);
} catch (InvalidCallbackException $e) { /* no DIGEST */ }
  catch (InvalidDigestException   $e) { /* bad sig   */ }
```

---

## SOAP error handling

Errors from the gateway arrive as `SoapFault`. The `serviceException` detail is automatically verified and surfaced as a `ServiceException` (which extends `Exception`) carrying `primaryReturnCode` and `secondaryReturnCode`.

```php
use Pronnect\GpWebPay\ServiceException;
use SoapFault;

try {
    $response = $gateway->createPaymentLink($request);
} catch (SoapFault $e) {
    if ($e->detail->serviceException ?? null) {
        /** @var ServiceException $ex */
        $ex = $e->detail->serviceException;
        echo $ex->getPrimaryReturnCode();    // e.g. "28"
        echo $ex->getSecondaryReturnCode();
        echo $ex->getMessage();              // human-readable from codes.xml
    }
}
```

---

## Demo application

A full interactive demo is included in the `demo/` directory. It lets you test both API modes against the GP Webpay **test gateway** using a real merchant certificate — no real charges are made.

### Features

- Merchant configuration form (provider, certificate, return URL)
- **SOAP WS API:** create payment links — direct capture or pre-authorisation; register card token (CoF) or recurring master; refund, capture, auth reversal, recurring charge, CoF re-charge
- **HTTP API:** create signed redirect orders (GET redirect or POST form), with return URL callback verification
- SQLite payment history with per-payment action panel
- Return URL handler at `/return` — verifies DIGEST/DIGEST1 signature, updates DB
- Full SOAP request/response logging to stdout (visible in Docker logs)
- Error messages with primary/secondary return code descriptions from `resources/xml/codes.xml`

### Start the demo

```bash
docker-compose -f docker-compose.demo.yml up --build
```

Open **http://localhost:8080** in your browser.

Set the **Return URL** in the configuration page to `http://localhost:8080/return`.

### Test cards

Use test cards on the GP Webpay payment page — do not enter real card details.

Full list: [developer.globalpayments.com → Test Card Numbers](https://developer.globalpayments.com/ecommerce/resources/test-card-numbers)

| Card | Number | Expiry | CVV | Result |
|---|---|---|---|---|
| Visa | `4263970000005262` | 02/26 | 100 | Approved |
| Mastercard | `5425233430109903` | 02/26 | 100 | Approved |
| Visa | `4000120000001154` | 02/26 | 100 | Declined |
| Mastercard | `5114610000004934` | 02/26 | 100 | Declined |

3D password (if requested): `Secure3D`

---

## Development

```bash
# Install dependencies
composer install

# Run all tests (427 tests, 771 assertions)
./vendor/bin/phpunit

# Run a single test file
./vendor/bin/phpunit tests/Unit/GatewayTest.php

# Run via Docker (no local PHP required)
docker-compose up --build
```

Tests require certificate paths set via environment variables (see `phpunit.xml`):

| Variable | Description |
|---|---|
| `GPWEBPAY_PRIVATE_KEY` | GPE public key path (for response verification) |
| `GPWEBPAY_MERCHANT_PRIVATE_KEY` | Merchant private key path (for request signing) |
| `GPWEBPAY_MERCHANT_PRIVATE_KEY_PASSWORD` | Merchant private key password |

---

## WSDL coverage (SOAP)

All 32 active WSDL operations are implemented. `processTokenPayment` exists in the WSDL but is deprecated by GP Webpay — use `processCardOnFilePayment` instead.

---

## License

MIT © [Pronnect s.r.o.](mailto:info@pronnect.sk)

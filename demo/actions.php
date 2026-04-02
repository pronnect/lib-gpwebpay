<?php
declare(strict_types=1);

use Pronnect\GpWebPay\Request\AltTerminalData;
use Pronnect\GpWebPay\Request\AuthorizationReverseRequest;
use Pronnect\GpWebPay\Request\CaptureReverseRequest;
use Pronnect\GpWebPay\Request\BillingDetails;
use Pronnect\GpWebPay\Request\CardHolderData;
use Pronnect\GpWebPay\Request\CardholderDetails;
use Pronnect\GpWebPay\Request\CardOnFilePaymentRequest;
use Pronnect\GpWebPay\Request\CaptureRequest;
use Pronnect\GpWebPay\Request\MasterPaymentStatusRequest;
use Pronnect\GpWebPay\Request\PaymentDetailRequest;
use Pronnect\GpWebPay\Request\PaymentInfo;
use Pronnect\GpWebPay\Request\PaymentLinkRequest;
use Pronnect\GpWebPay\Request\PaymentStatusRequest;
use Pronnect\GpWebPay\Request\RecurringPaymentRequest;
use Pronnect\GpWebPay\Request\RefundRequest;
use Pronnect\GpWebPay\Request\SubMerchantData;
use Pronnect\GpWebPay\Request\TokenRevokeRequest;
use Pronnect\GpWebPay\Request\TokenStatusRequest;
use Pronnect\GpWebPay\Http\Request\AddInfo                        as HttpAddInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\BillingDetails         as HttpBillingDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderDetails      as HttpCardholderDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\CardholderInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\PaymentInfo            as HttpPaymentInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShippingDetails        as HttpShippingDetails;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartInfo;
use Pronnect\GpWebPay\Http\Request\AddInfo\ShoppingCartItem;
use Pronnect\GpWebPay\Http\Request\CardPaymentRequest             as HttpCardPaymentRequest;
use Pronnect\GpWebPay\Http\Request\CardVerificationRequest        as HttpCardVerificationRequest;
use Pronnect\GpWebPay\Response\CardOnFilePaymentFaultDetail;

/**
 * Builds an HttpAddInfo object from POST fields using the given prefix.
 * Returns null if no AddInfo fields are filled in.
 */
function buildHttpAddInfo(string $prefix): ?HttpAddInfo
{
    $chName      = trim($_POST["{$prefix}ch_name"]       ?? '');
    $chEmail     = trim($_POST["{$prefix}ch_email"]      ?? '');
    $chPhoneCtry = trim($_POST["{$prefix}ch_phone_country"] ?? '');
    $chPhone     = trim($_POST["{$prefix}ch_phone"]      ?? '');
    $billName    = trim($_POST["{$prefix}billing_name"]      ?? '');
    $billAddr    = trim($_POST["{$prefix}billing_address1"]  ?? '');
    $billCity    = trim($_POST["{$prefix}billing_city"]      ?? '');
    $billZip     = trim($_POST["{$prefix}billing_postalCode"] ?? '');
    $billCtry    = trim($_POST["{$prefix}billing_country"]   ?? '');
    $billPhone   = trim($_POST["{$prefix}billing_phone"]     ?? '');
    $shipName    = trim($_POST["{$prefix}shipping_name"]     ?? '');
    $shipAddr    = trim($_POST["{$prefix}shipping_address1"] ?? '');
    $shipCity    = trim($_POST["{$prefix}shipping_city"]     ?? '');
    $shipZip     = trim($_POST["{$prefix}shipping_postalCode"] ?? '');
    $shipCtry    = trim($_POST["{$prefix}shipping_country"]  ?? '');
    $shipPhone   = trim($_POST["{$prefix}shipping_phone"]    ?? '');
    $txType      = trim($_POST["{$prefix}tx_type"]    ?? '');
    $itemDesc    = trim($_POST["{$prefix}item_desc"]  ?? '');
    $itemQty     = trim($_POST["{$prefix}item_qty"]   ?? '');
    $itemPrice   = trim($_POST["{$prefix}item_price"] ?? '');

    $hasPhone      = $chPhoneCtry !== '' && $chPhone !== '';
    $hasCardholder = $chName !== '' || $chEmail !== '' || $hasPhone;
    $hasBilling    = $billName !== '' || $billAddr !== '' || $billCity !== '' || $billZip !== '' || $billCtry !== '' || $billPhone !== '';
    $hasShipping   = $shipName !== '' || $shipAddr !== '' || $shipCity !== '' || $shipZip !== '' || $shipCtry !== '' || $shipPhone !== '';
    $hasPayment    = $txType !== '';
    $hasCart       = $itemDesc !== '' && $itemQty !== '' && $itemPrice !== '';

    if (!$hasCardholder && !$hasBilling && !$hasShipping && !$hasPayment && !$hasCart) {
        return null;
    }

    $addInfo = new HttpAddInfo();

    if ($hasCardholder || $hasBilling || $hasShipping) {
        $cardholderInfo = new CardholderInfo();

        if ($hasCardholder) {
            $details = new HttpCardholderDetails();
            if ($chName  !== '') $details->setName($chName);
            if ($chEmail !== '') $details->setEmail($chEmail);
            if ($hasPhone)       $details->setPhone($chPhoneCtry, $chPhone);
            $cardholderInfo->setCardholderDetails($details);
        }

        if ($hasBilling) {
            $billing = new HttpBillingDetails();
            if ($billName  !== '') $billing->setName($billName);
            if ($billAddr  !== '') $billing->setAddress1($billAddr);
            if ($billCity  !== '') $billing->setCity($billCity);
            if ($billZip   !== '') $billing->setPostalCode($billZip);
            if ($billCtry  !== '') $billing->setCountry($billCtry);
            if ($billPhone !== '') $billing->setPhone($billPhone);
            $cardholderInfo->setBillingDetails($billing);
        }

        if ($hasShipping) {
            $shipping = new HttpShippingDetails();
            if ($shipName  !== '') $shipping->setName($shipName);
            if ($shipAddr  !== '') $shipping->setAddress1($shipAddr);
            if ($shipCity  !== '') $shipping->setCity($shipCity);
            if ($shipZip   !== '') $shipping->setPostalCode($shipZip);
            if ($shipCtry  !== '') $shipping->setCountry($shipCtry);
            if ($shipPhone !== '') $shipping->setPhone($shipPhone);
            $cardholderInfo->setShippingDetails($shipping);
        }

        $addInfo->setCardholderInfo($cardholderInfo);
    }

    if ($hasPayment) {
        $addInfo->setPaymentInfo((new HttpPaymentInfo())->setTransactionType($txType));
    }

    if ($hasCart) {
        $priceCents = (int)round((float)str_replace(',', '.', $itemPrice) * 100);
        $addInfo->setShoppingCartInfo(
            (new ShoppingCartInfo())->addItem(new ShoppingCartItem($itemDesc, (int)$itemQty, $priceCents))
        );
    }

    return $addInfo;
}

// ─── Route variables ───────────────────────────────────────────────────────────

$page   = $_GET['page'] ?? (hasConfig() ? 'list' : 'config');
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = $_POST['action'] ?? '';

// ─── Save config ──────────────────────────────────────────────────────────────

if ($action === 'save_config') {
    $_SESSION['cfg'] = [
        'provider'             => trim($_POST['provider']),
        'merchant_number'      => trim($_POST['merchant_number']),
        'merchant_private_key' => trim($_POST['merchant_private_key']),
        'merchant_key_password'=> trim($_POST['merchant_key_password']),
        'gpe_public_key'       => trim($_POST['gpe_public_key']),
        'return_url'           => trim($_POST['return_url']),
    ];
    flash('ok', 'Configuration saved.');
    redirect('?page=list');
}

// ─── Create HTTP API redirect order ───────────────────────────────────────────

if ($action === 'create_http_order' && hasConfig()) {
    try {
        $cents       = amountCents($_POST['amount']);
        $orderNumber = (int)(trim($_POST['order_number']) ?: time());
        $depositFlag = (int)$_POST['deposit_flag'];
        $currency    = (int)$_POST['currency_code'];
        $returnUrl   = $_SESSION['cfg']['return_url'];

        $request = new HttpCardPaymentRequest(
            orderNumber: $orderNumber,
            amount:      $cents,
            currency:    $currency,
            depositFlag: $depositFlag,
            url:         $returnUrl,
        );

        $userParam1 = trim($_POST['userparam1'] ?? '');
        if (trim($_POST['description'] ?? '') !== '') {
            $request->setDescription(trim($_POST['description']));
        }
        if ($userParam1 !== '') {
            $request->setUserParam1($userParam1);
        }
        if (trim($_POST['lang'] ?? '') !== '') {
            $request->setLang(trim($_POST['lang']));
        }
        if (trim($_POST['md'] ?? '') !== '') {
            $request->setMd(trim($_POST['md']));
        }
        if (trim($_POST['email'] ?? '') !== '') {
            $request->setEmail(trim($_POST['email']));
        }

        $addInfo = buildHttpAddInfo('addinfo_');
        if ($addInfo !== null) {
            $request->setAddInfo($addInfo->toXml());
        }

        $gw = makeHttpGateway();

        // Map USERPARAM1 → registered_for (same convention as SOAP demo)
        $registeredFor = match ($userParam1) {
            'R'     => 'recurring',
            'T', 'S' => 'token',
            default  => null,
        };

        if ($request->getAddInfo() !== null) {
            // ADDINFO requires POST — build form params and emit a self-submitting form
            $params = $gw->getFormParams($request);
            $pdo->prepare(
                'INSERT INTO payments (type, payment_number, order_number, amount, currency_code, registered_for, http_redirect_url)
                 VALUES (:type, :pn, :on, :amt, :cur, :reg, :rurl)'
            )->execute([
                'type' => 'http_order',
                'pn'   => (string)$orderNumber,
                'on'   => (string)$orderNumber,
                'amt'  => $cents,
                'cur'  => $currency,
                'reg'  => $registeredFor,
                'rurl' => $gw->getHttpUri(),
            ]);
            emitPostRedirect($gw->getHttpUri(), $params);
        }

        // GET redirect (no ADDINFO)
        $redirectUrl = $gw->getRedirectUrl($request);

        // Save order before redirecting so the return handler can match it by ORDERNUMBER
        $pdo->prepare(
            'INSERT INTO payments (type, payment_number, order_number, amount, currency_code, registered_for, http_redirect_url)
             VALUES (:type, :pn, :on, :amt, :cur, :reg, :rurl)'
        )->execute([
            'type' => 'http_order',
            'pn'   => (string)$orderNumber,
            'on'   => (string)$orderNumber,
            'amt'  => $cents,
            'cur'  => $currency,
            'reg'  => $registeredFor,
            'rurl' => $redirectUrl,
        ]);

        // Redirect browser to GP Webpay
        redirect($redirectUrl);
    } catch (Throwable $e) {
        flash('err', $e->getMessage());
        redirect('?page=new');
    }
}

// ─── Create HTTP API card verification (CARD_VERIFICATION — CoF / recurring reg) ──

if ($action === 'create_card_verification' && hasConfig()) {
    try {
        $orderNumber = (int)(trim($_POST['cv_order_number']) ?: time());
        $returnUrl   = $_SESSION['cfg']['return_url'];

        $request = new HttpCardVerificationRequest(
            orderNumber: $orderNumber,
            url:         $returnUrl,
        );

        $userParam1 = trim($_POST['cv_userparam1'] ?? '');
        if ($userParam1 !== '')                          $request->setUserParam1($userParam1);
        if (trim($_POST['cv_description'] ?? '') !== '') $request->setDescription(trim($_POST['cv_description']));
        if (trim($_POST['cv_lang']        ?? '') !== '') $request->setLang(trim($_POST['cv_lang']));
        if (trim($_POST['cv_md']          ?? '') !== '') $request->setMd(trim($_POST['cv_md']));
        if (trim($_POST['cv_email']       ?? '') !== '') $request->setEmail(trim($_POST['cv_email']));

        $cvAddInfo = buildHttpAddInfo('cv_addinfo_');
        if ($cvAddInfo !== null) {
            $request->setAddInfo($cvAddInfo->toXml());
        }

        $gw = makeHttpGateway();

        $registeredFor = match ($userParam1) {
            'R'      => 'recurring',
            'T', 'S' => 'token',
            default  => null,
        };

        if ($request->getAddInfo() !== null) {
            $params = $gw->getFormParams($request);
            $pdo->prepare(
                'INSERT INTO payments (type, payment_number, order_number, amount, currency_code, registered_for, http_redirect_url)
                 VALUES (:type, :pn, :on, :amt, :cur, :reg, :rurl)'
            )->execute([
                'type' => 'card_verification',
                'pn'   => (string)$orderNumber,
                'on'   => (string)$orderNumber,
                'amt'  => 0,
                'cur'  => 0,
                'reg'  => $registeredFor,
                'rurl' => $gw->getHttpUri(),
            ]);
            emitPostRedirect($gw->getHttpUri(), $params);
        }

        $redirectUrl = $gw->getRedirectUrl($request);

        $pdo->prepare(
            'INSERT INTO payments (type, payment_number, order_number, amount, currency_code, registered_for, http_redirect_url)
             VALUES (:type, :pn, :on, :amt, :cur, :reg, :rurl)'
        )->execute([
            'type' => 'card_verification',
            'pn'   => (string)$orderNumber,
            'on'   => (string)$orderNumber,
            'amt'  => 0,
            'cur'  => 0,
            'reg'  => $registeredFor,
            'rurl' => $redirectUrl,
        ]);

        redirect($redirectUrl);
    } catch (Throwable $e) {
        flash('err', $e->getMessage());
        redirect('?page=new');
    }
}

// ─── Create CoF payment from token selection (new.php) ────────────────────────

if ($action === 'create_cof' && hasConfig()) {
    $parentId = (int)($_POST['parent_id'] ?? 0);
    $parent   = paymentRow($pdo, $parentId);
    if (!$parent || empty($parent['token_data'])) {
        flash('err', 'Parent payment not found or has no token.');
        redirect('?page=new');
    }

    $cents     = amountCents($_POST['amount']);
    $pn        = (string)(time() * 10 + 3);
    $on        = (string)(time() * 10 + 4);
    $tokenData = $parent['token_data'];
    $currency  = (int)($_POST['currency_code'] ?? ($parent['currency_code'] ?: 978));

    $req = (new CardOnFilePaymentRequest())
        ->setPaymentNumber($pn)
        ->setOrderNumber($on)
        ->setAmount($cents)
        ->setCurrencyCode($currency)
        ->setCaptureFlag((int)($_POST['deposit_flag'] ?? 1))
        ->setTokenData($tokenData)
        ->setReturnUrl($_SESSION['cfg']['return_url']);

    // INSERT before SOAP call so the payment is visible immediately
    $pdo->prepare(
        'INSERT INTO payments (type,payment_number,order_number,amount,currency_code,token_data,last_status,parent_payment_id)
         VALUES (:type,:pn,:on,:amt,:cur,:tok,:status,:ppid)'
    )->execute([
        'type'   => 'card_on_file',
        'pn'     => $pn,
        'on'     => $on,
        'amt'    => $cents,
        'cur'    => $currency,
        'tok'    => $tokenData,
        'status' => 'PENDING',
        'ppid'   => $parentId,
    ]);
    $newId = (int)$pdo->lastInsertId();

    try {
        $gw   = makeGateway();
        $resp = $gw->processCardOnFilePayment($req);
        updatePayment($pdo, $newId, [
            'last_status'       => null,
            'token_data'        => $resp->getTokenData() ?? $tokenData,
            'auth_code'         => $resp->getAuthCode(),
            'last_api_response' => json_encode(['authCode' => $resp->getAuthCode(), 'tokenData' => $resp->getTokenData()]),
        ]);
        flashSoapLog($gw);
        flash('ok', 'CoF payment: ' . ($resp->getAuthCode() ?? '—'));
    } catch (SoapFault $e) {
        if (isset($gw)) flashSoapLog($gw);
        $cofFault = $e->detail->cardOnFilePaymentServiceException
            ?? $e->detail->cardOnFilePaymentFaultDetail
            ?? null;
        $authLink = $cofFault->authenticationLink ?? null;
        if ($authLink) {
            updatePayment($pdo, $newId, [
                'last_status'         => 'AUTH_REQUIRED',
                'authentication_link' => $authLink,
                'last_api_response'   => json_encode([
                    'prCode'             => $cofFault->primaryReturnCode ?? null,
                    'srCode'             => $cofFault->secondaryReturnCode ?? null,
                    'authenticationLink' => $authLink,
                ]),
            ]);
            flash('ok', 'Authentication required — click the 3DS link on the detail page.');
        } else {
            updatePayment($pdo, $newId, [
                'last_status'       => 'ERROR',
                'last_api_response' => json_encode(['error' => $e->getMessage()]),
            ]);
            flash('err', gpwpError($e));
        }
    } catch (Throwable $e) {
        updatePayment($pdo, $newId, ['last_status' => 'ERROR']);
        flash('err', $e->getMessage());
    }
    redirect('?page=detail&id=' . $newId);
}

// ─── Create recurring payment from master selection (new.php) ──────────────────

if ($action === 'create_recurring' && hasConfig()) {
    $parentId = (int)($_POST['parent_id'] ?? 0);
    $parent   = paymentRow($pdo, $parentId);
    if (!$parent) {
        flash('err', 'Parent payment not found.');
        redirect('?page=new');
    }

    $cents    = amountCents($_POST['amount']);
    $pn       = (string)(time() * 10 + 1);
    $on       = (string)(time() * 10 + 2);
    $currency = (int)($parent['currency_code'] ?: 978);

    // HTTP recurring masters return a TOKEN that serves as masterPaymentNumber
    $isHttpRecurring = in_array($parent['type'], ['http_order', 'card_verification'])
        && !empty($parent['token_data']);
    $masterPn = $isHttpRecurring
        ? $parent['token_data']
        : ($parent['master_payment_number'] ?? $parent['payment_number']);

    $req = (new RecurringPaymentRequest())
        ->setPaymentNumber($pn)
        ->setMasterPaymentNumber($masterPn)
        ->setOrderNumber($on)
        ->setAmount($cents)
        ->setCurrencyCode($currency)
        ->setCaptureFlag(1);

    // INSERT before SOAP call
    $pdo->prepare(
        'INSERT INTO payments (type,payment_number,order_number,amount,currency_code,master_payment_number,last_status,parent_payment_id)
         VALUES (:type,:pn,:on,:amt,:cur,:mpn,:status,:ppid)'
    )->execute([
        'type'   => 'recurring',
        'pn'     => $pn,
        'on'     => $on,
        'amt'    => $cents,
        'cur'    => $currency,
        'mpn'    => $masterPn,
        'status' => 'PENDING',
        'ppid'   => $parentId,
    ]);
    $newId = (int)$pdo->lastInsertId();

    try {
        $gw   = makeGateway();
        $resp = $gw->processRecurringPayment($req);
        updatePayment($pdo, $newId, [
            'last_status'       => null,
            'auth_code'         => $resp->getAuthCode(),
            'last_api_response' => json_encode(['authCode' => $resp->getAuthCode(), 'traceId' => $resp->getTraceId()]),
        ]);
        flashSoapLog($gw);
        flash('ok', 'Recurring payment created. AuthCode: ' . ($resp->getAuthCode() ?? '—'));
    } catch (Throwable $e) {
        if (isset($gw)) flashSoapLog($gw);
        updatePayment($pdo, $newId, [
            'last_status'       => 'ERROR',
            'last_api_response' => json_encode(['error' => $e->getMessage()]),
        ]);
        flash('err', gpwpError($e));
    }
    redirect('?page=detail&id=' . $newId);
}

// ─── Create payment link (SOAP WS API) ────────────────────────────────────────

if ($action === 'create_link' && hasConfig()) {
    try {
        $gw            = makeGateway();
        $cents         = amountCents($_POST['amount']);
        $paymentNumber = trim($_POST['payment_number']) ?: (string)time();
        $orderNumber   = trim($_POST['order_number'])   ?: (string)(time() + 1);

        $req = (new PaymentLinkRequest())
            ->setPaymentNumber($paymentNumber)
            ->setAmount($cents)
            ->setCurrencyCode((int)$_POST['currency_code'])
            ->setCaptureFlag((bool)(int)$_POST['capture_flag'])
            ->setOrderNumber($orderNumber)
            ->setUrl($_SESSION['cfg']['return_url'])
            ->setPaymentExpiry(trim($_POST['payment_expiry']));

        if (trim($_POST['description'] ?? '') !== '') $req->setDescription(trim($_POST['description']));
        if (trim($_POST['email']       ?? '') !== '') $req->setEmail(trim($_POST['email']));
        if ($_POST['registration'] === 'recurring') $req->setRegisterRecurring(true);
        if ($_POST['registration'] === 'token')     $req->setRegisterToken(true);
        if (trim($_POST['merchant_email']       ?? '') !== '') $req->setMerchantEmail(trim($_POST['merchant_email']));
        if (trim($_POST['language']             ?? '') !== '') $req->setLanguage(trim($_POST['language']));
        if (trim($_POST['default_pay_method']   ?? '') !== '') $req->setDefaultPayMethod(trim($_POST['default_pay_method']));
        if (trim($_POST['disabled_pay_methods'] ?? '') !== '') $req->setDisabledPayMethods(trim($_POST['disabled_pay_methods']));
        if (trim($_POST['pay_methods']          ?? '') !== '') $req->setPayMethods(trim($_POST['pay_methods']));

        $resp = $gw->createPaymentLink($req);
        $link = $resp->getPaymentLink();

        $captureFlag  = (bool)(int)$_POST['capture_flag'];
        $registration = $_POST['registration'] ?: null;
        $st = $pdo->prepare(
            'INSERT INTO payments (type,registered_for,payment_number,order_number,amount,currency_code,payment_link)
             VALUES (:type,:reg,:pn,:on,:amt,:cur,:link)'
        );
        $st->execute([
            'type' => $captureFlag ? 'link' : 'preauth',
            'reg'  => $registration,
            'pn'   => $paymentNumber,
            'on'   => $orderNumber,
            'amt'  => $cents,
            'cur'  => (int)$_POST['currency_code'],
            'link' => $link,
        ]);
        $newId = (int)$pdo->lastInsertId();
        flashSoapLog($gw);
        flash('ok', 'Payment link created.');
        redirect('?page=detail&id=' . $newId);
    } catch (Throwable $e) {
        if (isset($gw)) flashSoapLog($gw);
        flash('err', gpwpError($e));
        redirect('?page=new');
    }
}

// ─── Actions on existing payment ──────────────────────────────────────────────

if ($action && $id && hasConfig()) {
    $row = paymentRow($pdo, $id);
    if ($row) {
        try {
            $gw = makeGateway();

            switch ($action) {

                case 'check_status':
                    $resp = $gw->getPaymentStatus(
                        (new PaymentStatusRequest())->setPaymentNumber($row['payment_number'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_api_response'=> json_encode(['status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Status: ' . ($resp->getStatus() ?? '—'));
                    break;

                case 'check_detail':
                    $resp = $gw->getPaymentDetail(
                        (new PaymentDetailRequest())->setPaymentNumber($row['payment_number'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_state'       => $resp->getState(),
                        'last_sub_status'  => $resp->getSubStatus(),
                        'pan_masked'       => $resp->getPanMasked(),
                        'brand_name'       => $resp->getBrandName(),
                        'auth_code'        => $resp->getApproveCode(),
                        'last_api_response'=> json_encode([
                            'state'       => $resp->getState(),
                            'status'      => $resp->getStatus(),
                            'subStatus'   => $resp->getSubStatus(),
                            'panMasked'   => $resp->getPanMasked(),
                            'brandName'   => $resp->getBrandName(),
                            'approveCode' => $resp->getApproveCode(),
                            'payAmt'      => $resp->getPaymentAmount(),
                            'approveAmt'  => $resp->getApproveAmount(),
                            'captureAmt'  => $resp->getCaptureAmount(),
                            'refundAmt'   => $resp->getRefundAmount(),
                            'paymentTime' => $resp->getPaymentTime(),
                        ]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Detail loaded: ' . ($resp->getState() ?? '—') . ' / ' . ($resp->getStatus() ?? '—'));
                    break;

                case 'refund':
                    $cents = amountCents($_POST['refund_amount'] ?? '0');
                    $req   = (new RefundRequest())->setPaymentNumber($row['payment_number']);
                    if ($cents > 0) $req->setAmount($cents);
                    $resp  = $gw->processRefund($req);
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_state'       => $resp->getState(),
                        'last_api_response'=> json_encode(['state' => $resp->getState(), 'status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Refund processed: ' . ($resp->getState() ?? '—'));
                    break;

                case 'capture_reverse':
                    $captureNumber = (int)($_POST['capture_number'] ?? 1) ?: 1;
                    $resp = $gw->processCaptureReverse(
                        (new CaptureReverseRequest())
                            ->setPaymentNumber($row['payment_number'])
                            ->setCaptureNumber($captureNumber)
                    );
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_state'       => $resp->getState(),
                        'last_api_response'=> json_encode(['state' => $resp->getState(), 'status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Capture reversed: ' . ($resp->getState() ?? '—'));
                    break;

                case 'capture':
                    $resp = $gw->processCapture(
                        (new CaptureRequest())
                            ->setPaymentNumber($row['payment_number'])
                            ->setAmount($row['amount'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_state'       => $resp->getState(),
                        'last_api_response'=> json_encode(['state' => $resp->getState(), 'status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Capture: ' . ($resp->getState() ?? '—'));
                    break;

                case 'auth_reverse':
                    $resp = $gw->processAuthorizationReverse(
                        (new AuthorizationReverseRequest())->setPaymentNumber($row['payment_number'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'      => $resp->getStatus(),
                        'last_state'       => $resp->getState(),
                        'last_api_response'=> json_encode(['state' => $resp->getState(), 'status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Auth reversal: ' . ($resp->getState() ?? '—'));
                    break;

                case 'recurring':
                    $cents         = amountCents($_POST['rec_amount']);
                    $paymentNumber = (string)(time() * 10 + 1);
                    $orderNumber   = (string)(time() * 10 + 2);
                    $masterPn      = $row['master_payment_number'] ?? $row['payment_number'];

                    $req = (new RecurringPaymentRequest())
                        ->setPaymentNumber($paymentNumber)
                        ->setMasterPaymentNumber($masterPn)
                        ->setOrderNumber($orderNumber)
                        ->setAmount($cents)
                        ->setCurrencyCode((int)($row['currency_code']))
                        ->setCaptureFlag(1);

                    // INSERT before SOAP call
                    $pdo->prepare(
                        'INSERT INTO payments (type,payment_number,order_number,amount,currency_code,master_payment_number,last_status,parent_payment_id)
                         VALUES (:type,:pn,:on,:amt,:cur,:mpn,:status,:ppid)'
                    )->execute([
                        'type'   => 'recurring',
                        'pn'     => $paymentNumber,
                        'on'     => $orderNumber,
                        'amt'    => $cents,
                        'cur'    => (int)$row['currency_code'],
                        'mpn'    => $masterPn,
                        'status' => 'PENDING',
                        'ppid'   => $id,
                    ]);
                    $newId = (int)$pdo->lastInsertId();

                    $resp = $gw->processRecurringPayment($req);
                    updatePayment($pdo, $newId, [
                        'last_status'       => null,
                        'auth_code'         => $resp->getAuthCode(),
                        'last_api_response' => json_encode(['authCode' => $resp->getAuthCode(), 'traceId' => $resp->getTraceId()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Recurring payment created. AuthCode: ' . ($resp->getAuthCode() ?? '—'));
                    redirect('?page=detail&id=' . $newId);
                    break;

                case 'card_on_file':
                    $cents         = amountCents($_POST['cof_amount']);
                    $paymentNumber = (string)(time() * 10 + 3);
                    $orderNumber   = (string)(time() * 10 + 4);
                    $tokenData     = trim($_POST['cof_token'] ?? $row['token_data'] ?? '');
                    $cofCurrency   = (int)($_POST['cof_currency'] ?? ($row['currency_code'] ?: 978));

                    if ($tokenData === '') {
                        throw new InvalidArgumentException('Token data is empty. First obtain it via getPaymentDetail.');
                    }

                    $req = (new CardOnFilePaymentRequest())
                        ->setPaymentNumber($paymentNumber)
                        ->setOrderNumber($orderNumber)
                        ->setAmount($cents)
                        ->setCurrencyCode($cofCurrency)
                        ->setCaptureFlag(1)
                        ->setTokenData($tokenData)
                        ->setReturnUrl($_SESSION['cfg']['return_url']);

                    // Optional: SubMerchantData
                    if (trim($_POST['sub_merchant_id'] ?? '') !== '') {
                        $sm = new SubMerchantData();
                        $sm->setMerchantId(trim($_POST['sub_merchant_id']));
                        if (trim($_POST['sub_merchant_type']        ?? '') !== '') $sm->setMerchantType(trim($_POST['sub_merchant_type']));
                        if (trim($_POST['sub_merchant_name']        ?? '') !== '') $sm->setMerchantName(trim($_POST['sub_merchant_name']));
                        if (trim($_POST['sub_merchant_street']      ?? '') !== '') $sm->setMerchantStreet(trim($_POST['sub_merchant_street']));
                        if (trim($_POST['sub_merchant_city']        ?? '') !== '') $sm->setMerchantCity(trim($_POST['sub_merchant_city']));
                        if (trim($_POST['sub_merchant_postal_code'] ?? '') !== '') $sm->setMerchantPostalCode(trim($_POST['sub_merchant_postal_code']));
                        if (trim($_POST['sub_merchant_country']     ?? '') !== '') $sm->setMerchantCountry(trim($_POST['sub_merchant_country']));
                        if (trim($_POST['sub_merchant_web']         ?? '') !== '') $sm->setMerchantWeb(trim($_POST['sub_merchant_web']));
                        if (trim($_POST['sub_merchant_svc_number']  ?? '') !== '') $sm->setMerchantServiceNumber(trim($_POST['sub_merchant_svc_number']));
                        $req->setSubMerchantData($sm);
                    }

                    // Optional: CardHolderData
                    $chName  = trim($_POST['ch_name']  ?? '');
                    $chEmail = trim($_POST['ch_email'] ?? '');
                    if ($chName !== '' || $chEmail !== '') {
                        $chDetails = new CardholderDetails();
                        if ($chName  !== '') $chDetails->setName($chName);
                        if ($chEmail !== '') $chDetails->setEmail($chEmail);

                        $chd = new CardHolderData();
                        $chd->setCardholderDetails($chDetails);

                        if (trim($_POST['address_match'] ?? '') !== '') {
                            $chd->setAddressMatch(trim($_POST['address_match']));
                        }

                        // Optional billing address
                        $billingName = trim($_POST['billing_name']        ?? '');
                        $billingAddr = trim($_POST['billing_address1']    ?? '');
                        $billingCity = trim($_POST['billing_city']        ?? '');
                        $billingZip  = trim($_POST['billing_postal_code'] ?? '');
                        $billingCtry = trim($_POST['billing_country']     ?? '');
                        if ($billingName !== '' || $billingAddr !== '') {
                            $bd = new BillingDetails();
                            if ($billingName !== '') $bd->setName($billingName);
                            if ($billingAddr !== '') $bd->setAddress1($billingAddr);
                            if ($billingCity !== '') $bd->setCity($billingCity);
                            if ($billingZip  !== '') $bd->setPostalCode($billingZip);
                            if ($billingCtry !== '') $bd->setCountry($billingCtry);
                            $chd->setBillingDetails($bd);
                        }

                        $req->setCardHolderData($chd);
                    }

                    // Optional: PaymentInfo (3DS2 order metadata)
                    $txType      = trim($_POST['payment_transaction_type']      ?? '');
                    $shipInd     = trim($_POST['payment_shipping_indicator']     ?? '');
                    $delivTime   = trim($_POST['payment_delivery_timeframe']     ?? '');
                    $recurExpiry = trim($_POST['payment_recurring_expiry']       ?? '');
                    if ($txType !== '' || $shipInd !== '' || $delivTime !== '' || $recurExpiry !== '') {
                        $pi = new PaymentInfo();
                        if ($txType      !== '') $pi->setTransactionType($txType);
                        if ($shipInd     !== '') $pi->setShippingIndicator($shipInd);
                        if ($delivTime   !== '') $pi->setDeliveryTimeframe($delivTime);
                        if ($recurExpiry !== '') $pi->setRecurringExpiry($recurExpiry);
                        $req->setPaymentInfo($pi);
                    }

                    // Optional: AltTerminalData
                    if (trim($_POST['alt_terminal_id'] ?? '') !== '') {
                        $atd = new AltTerminalData();
                        $atd->setTerminalId(trim($_POST['alt_terminal_id']));
                        if (trim($_POST['alt_terminal_owner'] ?? '') !== '') $atd->setTerminalOwner(trim($_POST['alt_terminal_owner']));
                        if (trim($_POST['alt_terminal_city']  ?? '') !== '') $atd->setTerminalCity(trim($_POST['alt_terminal_city']));
                        $req->setAltTerminalData($atd);
                    }

                    // INSERT before SOAP call
                    $pdo->prepare(
                        'INSERT INTO payments (type,payment_number,order_number,amount,currency_code,token_data,last_status,parent_payment_id)
                         VALUES (:type,:pn,:on,:amt,:cur,:tok,:status,:ppid)'
                    )->execute([
                        'type'   => 'card_on_file',
                        'pn'     => $paymentNumber,
                        'on'     => $orderNumber,
                        'amt'    => $cents,
                        'cur'    => $cofCurrency,
                        'tok'    => $tokenData,
                        'status' => 'PENDING',
                        'ppid'   => $id,
                    ]);
                    $newId = (int)$pdo->lastInsertId();

                    try {
                        $resp = $gw->processCardOnFilePayment($req);
                        updatePayment($pdo, $newId, [
                            'last_status'       => null,
                            'token_data'        => $resp->getTokenData() ?? $tokenData,
                            'auth_code'         => $resp->getAuthCode(),
                            'last_api_response' => json_encode(['authCode' => $resp->getAuthCode(), 'tokenData' => $resp->getTokenData()]),
                        ]);
                        flashSoapLog($gw);
                        flash('ok', 'Card-on-file payment: ' . ($resp->getAuthCode() ?? '—'));
                    } catch (SoapFault $e) {
                        flashSoapLog($gw);
                        $cofFault = $e->detail->cardOnFilePaymentServiceException
                            ?? $e->detail->cardOnFilePaymentFaultDetail
                            ?? null;
                        $authLink = $cofFault->authenticationLink ?? null;
                        if ($authLink) {
                            updatePayment($pdo, $newId, [
                                'last_status'         => 'AUTH_REQUIRED',
                                'authentication_link' => $authLink,
                                'last_api_response'   => json_encode([
                                    'prCode'             => $cofFault->primaryReturnCode ?? null,
                                    'srCode'             => $cofFault->secondaryReturnCode ?? null,
                                    'authenticationLink' => $authLink,
                                ]),
                            ]);
                            flash('ok', 'Authentication required — click the 3DS link on the detail page.');
                            redirect('?page=detail&id=' . $newId);
                        }
                        updatePayment($pdo, $newId, [
                            'last_status'       => 'ERROR',
                            'last_api_response' => json_encode(['error' => $e->getMessage()]),
                        ]);
                        flash('err', gpwpError($e));
                    }
                    redirect('?page=detail&id=' . $newId);
                    break;

                case 'token_status':
                    $tokenData = trim($_POST['ts_token'] ?? $row['token_data'] ?? '');
                    if ($tokenData === '') {
                        throw new InvalidArgumentException('Token data is empty.');
                    }
                    $resp = $gw->getTokenStatus(
                        (new TokenStatusRequest())->setTokenData($tokenData)
                    );
                    updatePayment($pdo, $id, [
                        'last_status'       => $resp->getStatus(),
                        'last_api_response' => json_encode(['status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Token status: ' . ($resp->getStatus() ?? '—'));
                    break;

                case 'token_revoke':
                    $tokenData = trim($_POST['tr_token'] ?? $row['token_data'] ?? '');
                    if ($tokenData === '') {
                        throw new InvalidArgumentException('Token data is empty.');
                    }
                    $resp = $gw->processTokenRevoke(
                        (new TokenRevokeRequest())->setTokenData($tokenData)
                    );
                    updatePayment($pdo, $id, [
                        'last_status'       => $resp->getStatus(),
                        'token_data'        => null,
                        'last_api_response' => json_encode(['status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Token revoked: ' . ($resp->getStatus() ?? '—'));
                    break;

                case 'master_status':
                    $resp = $gw->getMasterPaymentStatus(
                        (new MasterPaymentStatusRequest())->setPaymentNumber($row['payment_number'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'       => $resp->getStatus(),
                        'last_api_response' => json_encode(['status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Master status: ' . ($resp->getStatus() ?? '—'));
                    break;

                case 'master_revoke':
                    $resp = $gw->processMasterPaymentRevoke(
                        (new MasterPaymentStatusRequest())->setPaymentNumber($row['payment_number'])
                    );
                    updatePayment($pdo, $id, [
                        'last_status'       => $resp->getStatus(),
                        'last_api_response' => json_encode(['status' => $resp->getStatus()]),
                    ]);
                    flashSoapLog($gw);
                    flash('ok', 'Master revoke: ' . ($resp->getStatus() ?? '—'));
                    break;
            }
        } catch (Throwable $e) {
            if (isset($gw)) flashSoapLog($gw);
            flash('err', gpwpError($e));
        }
        redirect('?page=detail&id=' . $id);
    }
}

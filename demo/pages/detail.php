<?php declare(strict_types=1);

$row = paymentRow($pdo, $id);
if (!$row): ?>
  <div class="flash-err">Payment not found.</div>
<?php return; endif;

$lastResp = $row['last_api_response'] ? json_decode($row['last_api_response'], true) : null;

// Human-readable status text: prefer RESULTTEXT from GP Webpay callback, then local mapping
// RESULTTEXT comes from GP Webpay callback; synthetic statuses are set by the demo
$statusText = $lastResp['RESULTTEXT'] ?? match($row['last_status']) {
    'PENDING'       => 'Pending — not yet sent to gateway',
    'AUTH_REQUIRED' => '3DS authentication required',
    'ERROR'         => $lastResp['error'] ?? null,
    default         => null,
};

// For HTTP redirect orders: parse the signed request params from the stored redirect URL
$httpRequestParams = null;
if (!empty($row['http_redirect_url'])) {
    $qs = parse_url($row['http_redirect_url'], PHP_URL_QUERY);
    if ($qs) {
        parse_str($qs, $httpRequestParams);
    }
}
?>

<div style="display:flex;gap:.5rem;align-items:center;margin-bottom:.75rem">
  <a href="?page=list" style="color:#6b7280;font-size:.85rem">← Back to list</a>
</div>

<!-- Info card -->
<div class="card">
  <div class="card-header">
    Payment #<?= $row['id'] ?> &nbsp;
    <?php echo match($row['type']) {
        'recurring'    => '<span class="pill pill-warn">Recurring</span>',
        'card_on_file' => '<span class="pill pill-ok">Card-on-File</span>',
        'preauth'      => '<span class="pill pill-warn">Pre-auth (reservation)</span>',
        'http_order'        => '<span class="pill" style="background:#dbeafe;color:#1e40af">HTTP API Order</span>',
        'card_verification' => '<span class="pill" style="background:#f0fdf4;color:#166534">Card Verification (CoF Reg)</span>',
        default        => '<span class="pill pill-gray">SOAP Payment Link (capture)</span>',
    }; ?>
  </div>
  <div class="card-body">
    <dl class="dl">
      <dt>Payment #</dt>  <dd><code><?= esc($row['payment_number']) ?></code></dd>
      <dt>Order #</dt>    <dd><?= esc($row['order_number'] ?? '—') ?></dd>
      <dt>Amount</dt>     <dd><?= money((int)$row['amount'], (int)$row['currency_code']) ?></dd>
      <dt>Status</dt>     <dd>
        <?= $row['last_status'] ? esc($row['last_status']) : '—' ?>
        <?php if ($statusText): ?>
          <span style="color:#6b7280;font-size:.8rem">— <?= esc($statusText) ?></span>
        <?php endif; ?>
      </dd>
      <dt>State</dt>      <dd><?= $row['last_state']  ? esc($row['last_state'])  : '—' ?></dd>
      <dt>SubStatus</dt>  <dd><?= $row['last_sub_status'] ? esc($row['last_sub_status']) : '—' ?></dd>
      <dt>Card</dt>       <dd><?= $row['pan_masked'] ? esc($row['pan_masked']) . ' ' . esc($row['brand_name'] ?? '') : '—' ?></dd>
      <dt>Auth code</dt>  <dd><?= esc($row['auth_code'] ?? '—') ?></dd>
      <?php if (!empty($row['http_redirect_url'])): ?>
      <dt>HTTP redirect URL</dt>
      <dd style="word-break:break-all">
        <a href="<?= esc($row['http_redirect_url']) ?>" target="_blank" class="btn btn-success btn-sm" style="margin-bottom:.35rem">→ Go to GP Webpay</a>
        <div style="font-size:.72rem;color:#6b7280;margin-top:.25rem"><?= esc($row['http_redirect_url']) ?></div>
      </dd>
      <?php endif; ?>
      <?php if ($row['payment_link']): ?>
      <dt>SOAP payment link</dt><dd><a href="<?= esc($row['payment_link']) ?>" target="_blank"><?= esc($row['payment_link']) ?></a></dd>
      <?php endif; ?>
      <?php if ($row['parent_payment_id']): ?>
      <dt>Parent payment</dt><dd><a href="?page=detail&id=<?= (int)$row['parent_payment_id'] ?>">→ Payment #<?= (int)$row['parent_payment_id'] ?></a></dd>
      <?php endif; ?>
      <?php if (!empty($row['authentication_link'])): ?>
      <?php $authPending = $row['last_status'] === 'AUTH_REQUIRED'; ?>
      <dt>3DS Auth link</dt>
      <dd>
        <?php if ($authPending): ?>
          <a href="<?= esc($row['authentication_link']) ?>" target="_blank" class="btn btn-success btn-sm" style="margin-bottom:.35rem">→ Complete 3DS authentication</a>
        <?php else: ?>
          <span class="btn btn-success btn-sm" style="margin-bottom:.35rem;opacity:.4;cursor:not-allowed" title="Payment already finalized">→ Complete 3DS authentication</span>
        <?php endif; ?>
        <div style="font-size:.72rem;color:#6b7280;margin-top:.25rem;word-break:break-all"><?= esc($row['authentication_link']) ?></div>
      </dd>
      <?php endif; ?>
      <?php if ($row['master_payment_number']): ?>
      <dt>Master PAY #</dt><dd><code><?= esc($row['master_payment_number']) ?></code></dd>
      <?php endif; ?>
      <?php if ($row['token_data']): ?>
      <dt>Token data</dt> <dd><code style="font-size:.75rem;word-break:break-all"><?= esc($row['token_data']) ?></code></dd>
      <?php endif; ?>
      <dt>Created</dt>    <dd style="color:#6b7280"><?= esc($row['created_at']) ?></dd>
    </dl>

    <?php if ($httpRequestParams || $lastResp): ?>
    <div style="margin-top:1rem;display:grid;grid-template-columns:<?= ($httpRequestParams && $lastResp) ? '1fr 1fr' : '1fr' ?>;gap:1rem;align-items:start">
      <?php if ($httpRequestParams): ?>
      <div>
        <div style="font-size:.75rem;color:#6b7280;margin-bottom:.3rem">Request params (sent to GP Webpay)</div>
        <div class="resp-box" style="max-height:none;overflow:visible"><?= esc(json_encode($httpRequestParams, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></div>
      </div>
      <?php endif; ?>
      <?php if ($lastResp): ?>
      <div>
        <div style="font-size:.75rem;color:#6b7280;margin-bottom:.3rem">Last response (returned by GP Webpay)</div>
        <div class="resp-box" style="max-height:none;overflow:visible"><?= esc(json_encode($lastResp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!hasConfig()): ?>
  <div class="flash-err">Configuration missing — actions not available. <a href="?page=config">→ Configuration</a></div>
<?php return; endif;

// ── Visibility rules ─────────────────────────────────────────────────────────
$isHttpOrder        = $row['type'] === 'http_order';
$isCardVerification = $row['type'] === 'card_verification';
$isHttpLike         = $isHttpOrder || $isCardVerification;
$isPreauth          = $row['type'] === 'preauth';
$isLink             = $row['type'] === 'link';
$regForRecurring    = $row['registered_for'] === 'recurring';
$hasToken           = !empty($row['token_data']);

// SOAP operations work on any payment type — payment_number is the common identifier
$showRefund         = !$isCardVerification && !$isPreauth;   // processRefund — only for completed capture payments
$showCaptureReverse = !$isCardVerification && !$isPreauth;   // processCaptureReverse — reverse a capture
$showCapture        = $isPreauth;
$showAuthReverse    = $isPreauth;                            // processAuthorizationReverse — cancel a reservation
$showRecurring   = ($isLink || $isPreauth) && $regForRecurring;
$showCoF         = $hasToken;
$showToken       = $hasToken;
$showMaster      = ($isLink || $isPreauth) && $regForRecurring;
?>

<!-- Actions card -->
<div class="card">
  <div class="card-header">Actions</div>
  <div class="card-body">

    <?php if ($isHttpLike): ?>
    <!-- HTTP redirect context info -->
    <div class="action-section">
      <?php if ($isCardVerification): ?>
      <h4>Card Verification (CoF / Recurring Registration)</h4>
      <p style="font-size:.83rem;color:#475569;margin-bottom:.5rem">
        This is a <strong>CARD_VERIFICATION</strong> request — no funds are blocked. GP Webpay returns a TOKEN in the callback, which is saved here automatically.
      </p>
      <?php if ($row['registered_for'] === 'recurring'): ?>
      <p style="font-size:.82rem;color:#92400e;background:#fef3c7;padding:.4rem .75rem;border-radius:5px;margin-bottom:.5rem">
        🔄 Registered for <strong>recurring master</strong> (USERPARAM1=R). After verification GP Webpay issues a TOKEN — use it as <code>masterPaymentNumber</code> for subsequent MIT payments.
      </p>
      <?php else: ?>
      <p style="font-size:.82rem;color:#065f46;background:#d1fae5;padding:.4rem .75rem;border-radius:5px;margin-bottom:.5rem">
        💳 Registered for <strong>Card-on-File</strong> (USERPARAM1=<?= esc($row['registered_for'] === 'token' ? 'T/S' : '?') ?>). After verification GP Webpay returns a TOKEN — use it in subsequent <code>CardPaymentRequest::setToken()</code> calls.
      </p>
      <?php endif; ?>
      <?php if (!empty($row['token_data'])): ?>
      <p style="font-size:.82rem;margin-bottom:.5rem">
        Token: <code style="word-break:break-all"><?= esc($row['token_data']) ?></code>
      </p>
      <?php else: ?>
      <p style="font-size:.8rem;color:#6b7280;margin-bottom:.5rem">Token not yet received — complete the card verification first.</p>
      <?php endif; ?>
      <?php else: ?>
      <h4>HTTP API Order</h4>
      <p style="font-size:.83rem;color:#475569;margin-bottom:.5rem">
        This order was created via the HTTP API (redirect flow). The payment result is updated automatically when GP Webpay redirects back to the return URL.
      </p>
      <?php if ($row['registered_for'] === 'recurring'): ?>
      <p style="font-size:.82rem;color:#92400e;background:#fef3c7;padding:.4rem .75rem;border-radius:5px;margin-bottom:.5rem">
        🔄 Registered for <strong>recurring</strong> (USERPARAM1=R). After successful payment GP Webpay issues a master payment number — check <em>Last API response</em> for TOKENREGSTATUS and TOKEN fields.
      </p>
      <?php elseif ($row['registered_for'] === 'token'): ?>
      <p style="font-size:.82rem;color:#065f46;background:#d1fae5;padding:.4rem .75rem;border-radius:5px;margin-bottom:.5rem">
        💳 Registered for <strong>Card-on-File</strong> (USERPARAM1=T/S). After successful payment GP Webpay returns a TOKEN — it is saved automatically when the return URL callback is received.
      </p>
      <?php if (!empty($row['token_data'])): ?>
      <p style="font-size:.82rem;margin-bottom:.5rem">
        Token: <code style="word-break:break-all"><?= esc($row['token_data']) ?></code>
      </p>
      <?php else: ?>
      <p style="font-size:.8rem;color:#6b7280;margin-bottom:.5rem">Token not yet received — complete the payment first.</p>
      <?php endif; ?>
      <?php endif; ?>
      <?php endif; // end $isCardVerification / $isHttpOrder ?>
      <?php if (!empty($row['http_redirect_url'])): ?>
      <a href="<?= esc($row['http_redirect_url']) ?>" target="_blank" class="btn btn-success btn-sm">→ Re-open GP Webpay page</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Status check — available for all payment types -->
    <div class="action-section">
      <h4>Status check</h4>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <form method="post">
          <input type="hidden" name="action" value="check_status">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-neutral btn-sm">📋 getPaymentStatus</button>
        </form>
        <form method="post">
          <input type="hidden" name="action" value="check_detail">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-neutral btn-sm">🔍 getPaymentDetail</button>
        </form>
      </div>
    </div>

    <?php if ($showCaptureReverse): ?>
    <!-- Capture reverse — undo a captured payment -->
    <div class="action-section">
      <h4>Capture reverse</h4>
      <div class="hint" style="margin-bottom:.5rem">Reverses a captured (settled) payment. Sends processCaptureReverse.</div>
      <form method="post" class="inline-form">
        <input type="hidden" name="action" value="capture_reverse">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="field">
          <label>Capture number</label>
          <input type="number" name="capture_number" value="1" min="1" style="width:80px">
        </div>
        <button class="btn btn-warning btn-sm">↩ processCaptureReverse</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($showRefund): ?>
    <!-- Refund — partial or full -->
    <div class="action-section">
      <h4>Refund</h4>
      <div class="hint" style="margin-bottom:.5rem">Refunds a settled payment. Leave amount empty for full refund. Sends processRefund.</div>
      <form method="post" class="inline-form">
        <input type="hidden" name="action" value="refund">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="field">
          <label>Amount (empty = full refund)</label>
          <input type="text" name="refund_amount" value="" placeholder="e.g. 5.00" style="width:150px">
        </div>
        <button class="btn btn-danger btn-sm">↩ processRefund</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($showCapture || $showAuthReverse): ?>
    <!-- Capture + Auth reverse — pre-auth only -->
    <div class="action-section">
      <h4>Capture / Cancel reservation</h4>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <?php if ($showCapture): ?>
        <form method="post">
          <input type="hidden" name="action" value="capture">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-success btn-sm">✓ Capture</button>
        </form>
        <?php endif; ?>
        <?php if ($showAuthReverse): ?>
        <form method="post">
          <input type="hidden" name="action" value="auth_reverse">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-danger btn-sm">✕ Auth reversal</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($showRecurring): ?>
    <!-- Recurring — only when registered -->
    <div class="action-section">
      <h4>Recurring payment</h4>
      <div class="hint" style="margin-bottom:.6rem">
        This payment is the master. Uses its payment_number as masterPaymentNumber.
      </div>
      <form method="post" class="inline-form">
        <input type="hidden" name="action" value="recurring">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="field">
          <label>Amount for new payment</label>
          <input type="text" name="rec_amount" value="<?= money((int)$row['amount'], (int)$row['currency_code']) ?>" style="width:120px">
        </div>
        <button class="btn btn-primary btn-sm">↻ Recurring</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($showMaster): ?>
    <!-- Master payment management — recurring only -->
    <div class="action-section">
      <h4>Master payment (recurring management)</h4>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <form method="post">
          <input type="hidden" name="action" value="master_status">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-neutral btn-sm">📋 getMasterPaymentStatus</button>
        </form>
        <form method="post">
          <input type="hidden" name="action" value="master_revoke">
          <input type="hidden" name="id" value="<?= $id ?>">
          <button class="btn btn-danger btn-sm">✕ processMasterPaymentRevoke</button>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($showCoF): ?>
    <!-- Card-on-File payment — shown for any payment with a token (SOAP or HTTP) -->
    <div class="action-section">
      <h4>Card-on-File payment</h4>
      <form method="post">
        <input type="hidden" name="action" value="card_on_file">
        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- Required fields -->
        <div class="inline-form" style="margin-bottom:.75rem">
          <div class="field">
            <label>Amount</label>
            <input type="text" name="cof_amount" value="<?= money((int)$row['amount'], (int)($row['currency_code'] ?: 978)) ?>" style="width:130px">
          </div>
          <div class="field">
            <label>Currency</label>
            <select name="cof_currency">
              <?php foreach ($currencies as $code => $label): ?>
                <option value="<?= $code ?>" <?= ((int)($row['currency_code'] ?: 978) === $code) ? 'selected' : '' ?>><?= esc($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Token data</label>
            <input type="text" name="cof_token" value="<?= esc($row['token_data'] ?? '') ?>" style="width:280px">
          </div>
        </div>

        <!-- Optional: SubMerchantData -->
        <details style="margin-bottom:.5rem;border:1px solid #e2e8f0;border-radius:5px;padding:.6rem .75rem">
          <summary style="cursor:pointer;font-size:.82rem;font-weight:600;color:#475569">Sub-merchant data (optional)</summary>
          <div style="margin-top:.75rem">
            <div class="hint" style="margin-bottom:.5rem">Payment facilitator / sub-merchant identification. Fill merchantId to activate.</div>
            <div class="row3">
              <div class="field"><label>Merchant ID (1-15)</label><input type="text" name="sub_merchant_id" value="" maxlength="15"></div>
              <div class="field"><label>MCC type (4 digits)</label><input type="text" name="sub_merchant_type" value="" maxlength="4" placeholder="5411"></div>
              <div class="field"><label>Merchant name (1-22)</label><input type="text" name="sub_merchant_name" value="" maxlength="22"></div>
              <div class="field"><label>Street (1-25)</label><input type="text" name="sub_merchant_street" value="" maxlength="25"></div>
              <div class="field"><label>City (1-13)</label><input type="text" name="sub_merchant_city" value="" maxlength="13"></div>
              <div class="field"><label>Postal code (1-10)</label><input type="text" name="sub_merchant_postal_code" value="" maxlength="10"></div>
              <div class="field"><label>Country (ISO 3166-1 α2)</label><input type="text" name="sub_merchant_country" value="" maxlength="2" placeholder="CZ"></div>
              <div class="field"><label>Web (1-25)</label><input type="text" name="sub_merchant_web" value="" maxlength="25"></div>
              <div class="field"><label>Service number (digits)</label><input type="text" name="sub_merchant_svc_number" value="" maxlength="13"></div>
            </div>
          </div>
        </details>

        <!-- Optional: CardHolderData -->
        <details style="margin-bottom:.5rem;border:1px solid #e2e8f0;border-radius:5px;padding:.6rem .75rem">
          <summary style="cursor:pointer;font-size:.82rem;font-weight:600;color:#475569">Cardholder data — 3DS2 (optional)</summary>
          <div style="margin-top:.75rem">
            <div class="hint" style="margin-bottom:.5rem">Cardholder identity and billing address for 3DS2 authentication.</div>
            <div style="font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Cardholder identity</div>
            <div class="row3">
              <div class="field"><label>Name</label><input type="text" name="ch_name" value=""></div>
              <div class="field"><label>E-mail</label><input type="email" name="ch_email" value=""></div>
              <div class="field">
                <label>Address match (billing = shipping)</label>
                <select name="address_match">
                  <option value="">— unset —</option>
                  <option value="Y">Y — yes</option>
                  <option value="N">N — no</option>
                </select>
              </div>
            </div>
            <div style="font-size:.8rem;font-weight:600;color:#374151;margin:.6rem 0 .4rem">Billing address</div>
            <div class="row3">
              <div class="field"><label>Name</label><input type="text" name="billing_name" value=""></div>
              <div class="field"><label>Address line 1</label><input type="text" name="billing_address1" value=""></div>
              <div class="field"><label>City</label><input type="text" name="billing_city" value=""></div>
              <div class="field"><label>Postal code</label><input type="text" name="billing_postal_code" value=""></div>
              <div class="field"><label>Country (ISO 3166-1 α2)</label><input type="text" name="billing_country" value="" maxlength="2" placeholder="CZ"></div>
            </div>
          </div>
        </details>

        <!-- Optional: PaymentInfo -->
        <details style="margin-bottom:.5rem;border:1px solid #e2e8f0;border-radius:5px;padding:.6rem .75rem">
          <summary style="cursor:pointer;font-size:.82rem;font-weight:600;color:#475569">Payment info — 3DS2 metadata (optional)</summary>
          <div style="margin-top:.75rem">
            <div class="row2">
              <div class="field">
                <label>Transaction type</label>
                <select name="payment_transaction_type">
                  <option value="">— unset —</option>
                  <option value="01">01 — Goods / service purchase</option>
                  <option value="03">03 — Check acceptance</option>
                  <option value="10">10 — Account funding</option>
                  <option value="11">11 — Quasi-cash transaction</option>
                  <option value="28">28 — Prepaid activation / load</option>
                </select>
              </div>
              <div class="field">
                <label>Shipping indicator</label>
                <select name="payment_shipping_indicator">
                  <option value="">— unset —</option>
                  <option value="01">01 — Ship to billing address</option>
                  <option value="02">02 — Ship to verified non-billing</option>
                  <option value="03">03 — Ship to different non-billing</option>
                  <option value="04">04 — Ship to store (pick-up)</option>
                  <option value="05">05 — Digital goods (no shipping)</option>
                  <option value="06">06 — Travel / event tickets</option>
                  <option value="07">07 — Other</option>
                </select>
              </div>
              <div class="field">
                <label>Delivery timeframe</label>
                <select name="payment_delivery_timeframe">
                  <option value="">— unset —</option>
                  <option value="01">01 — Electronic delivery</option>
                  <option value="02">02 — Same day</option>
                  <option value="03">03 — Overnight</option>
                  <option value="04">04 — Two or more days</option>
                </select>
              </div>
              <div class="field">
                <label>Recurring expiry (YYYYMMDD)</label>
                <input type="text" name="payment_recurring_expiry" value="" placeholder="e.g. 20261231" maxlength="8">
              </div>
            </div>
          </div>
        </details>

        <!-- Optional: AltTerminalData -->
        <details style="margin-bottom:.75rem;border:1px solid #e2e8f0;border-radius:5px;padding:.6rem .75rem">
          <summary style="cursor:pointer;font-size:.82rem;font-weight:600;color:#475569">Alt terminal data (optional)</summary>
          <div style="margin-top:.75rem">
            <div class="hint" style="margin-bottom:.5rem">Alternative terminal identification. Fill Terminal ID to activate.</div>
            <div class="row3">
              <div class="field"><label>Terminal ID (max 8)</label><input type="text" name="alt_terminal_id" value="" maxlength="8"></div>
              <div class="field"><label>Terminal owner (max 22)</label><input type="text" name="alt_terminal_owner" value="" maxlength="22"></div>
              <div class="field"><label>Terminal city (max 13)</label><input type="text" name="alt_terminal_city" value="" maxlength="13"></div>
            </div>
          </div>
        </details>

        <button class="btn btn-primary btn-sm">💳 Card-on-File</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if ($showToken): ?>
    <!-- Token management — shown for any payment with a token (SOAP or HTTP) -->
    <div class="action-section">
      <h4>Token (Card-on-File)</h4>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <form method="post" class="inline-form">
          <input type="hidden" name="action" value="token_status">
          <input type="hidden" name="id" value="<?= $id ?>">
          <div class="field">
            <label>Token data</label>
            <input type="text" name="ts_token" value="<?= esc($row['token_data'] ?? '') ?>" style="width:240px">
          </div>
          <button class="btn btn-neutral btn-sm">📋 getTokenStatus</button>
        </form>
        <form method="post" class="inline-form">
          <input type="hidden" name="action" value="token_revoke">
          <input type="hidden" name="id" value="<?= $id ?>">
          <div class="field">
            <label>Token data</label>
            <input type="text" name="tr_token" value="<?= esc($row['token_data'] ?? '') ?>" style="width:240px">
          </div>
          <button class="btn btn-danger btn-sm">✕ processTokenRevoke</button>
        </form>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

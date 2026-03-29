<?php declare(strict_types=1);

if (!hasConfig()): ?>
  <div class="flash-err">Please configure the merchant first. <a href="?page=config">→ Configuration</a></div>
<?php else: ?>

<div class="card" style="border-left:4px solid #3b82f6">
  <div class="card-header" style="background:#eff6ff;color:#1e40af">ℹ️ Test environment — GP Webpay TEST gateway</div>
  <div class="card-body" style="font-size:.85rem;line-height:1.6">
    <p>On the GP Webpay payment page use a <strong>test card</strong>. Do not use real cards — no charge will be made.</p>
    <p style="margin-top:.5rem">
      Test card list: <a href="https://developer.globalpayments.com/ecommerce/resources/test-card-numbers" target="_blank" rel="noopener">developer.globalpayments.com → Test Card Numbers</a>
    </p>
    <p style="margin-top:.5rem">Most commonly used:</p>
    <table style="margin-top:.4rem;border-collapse:collapse;width:100%">
      <thead><tr style="background:#f8fafc">
        <th style="padding:.3rem .6rem;text-align:left;font-size:.75rem;color:#6b7280;border-bottom:1px solid #e2e8f0">Card</th>
        <th style="padding:.3rem .6rem;text-align:left;font-size:.75rem;color:#6b7280;border-bottom:1px solid #e2e8f0">Number</th>
        <th style="padding:.3rem .6rem;text-align:left;font-size:.75rem;color:#6b7280;border-bottom:1px solid #e2e8f0">Expiry</th>
        <th style="padding:.3rem .6rem;text-align:left;font-size:.75rem;color:#6b7280;border-bottom:1px solid #e2e8f0">CVV</th>
        <th style="padding:.3rem .6rem;text-align:left;font-size:.75rem;color:#6b7280;border-bottom:1px solid #e2e8f0">Result</th>
      </tr></thead>
      <tbody>
        <tr><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">Visa</td>       <td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9"><code>4263970000005262</code></td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">02/26</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">100</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">✅ Approved</td></tr>
        <tr><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">Mastercard</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9"><code>5425233430109903</code></td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">02/26</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">100</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">✅ Approved</td></tr>
        <tr><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">Visa</td>       <td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9"><code>4000120000001154</code></td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">02/26</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">100</td><td style="padding:.3rem .6rem;border-bottom:1px solid #f1f5f9">❌ Declined</td></tr>
        <tr><td style="padding:.3rem .6rem">Mastercard</td><td style="padding:.3rem .6rem"><code>5114610000004934</code></td><td style="padding:.3rem .6rem">02/26</td><td style="padding:.3rem .6rem">100</td><td style="padding:.3rem .6rem">❌ Declined</td></tr>
      </tbody>
    </table>
    <p style="margin-top:.5rem;color:#6b7280;font-size:.78rem">3D password for test cards: <code>Secure3D</code> (if requested)</p>
  </div>
</div>

<div class="card">
  <div class="card-header">New HTTP API Order <span style="font-size:.72rem;font-weight:normal;color:#6b7280">— redirect-based, no SOAP</span></div>
  <div class="card-body">
    <p style="font-size:.83rem;color:#475569;margin-bottom:.75rem">
      Builds a signed redirect URL and sends the browser directly to GP Webpay (HTTP API, GET). No SOAP call — signature is computed locally.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="create_http_order">
      <div class="row3">
        <div class="field">
          <label>Order number</label>
          <input type="number" name="order_number" value="<?= date('YmdHis') ?>" required min="1">
          <div class="hint">Numeric, returned in callback as ORDERNUMBER</div>
        </div>
        <div class="field">
          <label>Amount</label>
          <input type="text" name="amount" value="10.00" required>
          <div class="hint">In major units (e.g. 10.50)</div>
        </div>
        <div class="field">
          <label>Currency</label>
          <select name="currency_code">
            <?php foreach ($currencies as $code => $label): ?>
              <option value="<?= $code ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Deposit flag</label>
          <select name="deposit_flag">
            <option value="1">1 — Direct capture (immediate charge)</option>
            <option value="0">0 — Pre-auth (capture later)</option>
          </select>
        </div>
        <div class="field">
          <label>USERPARAM1 — registration</label>
          <select name="userparam1">
            <option value="">— none (standard payment) —</option>
            <option value="R">R — Register recurring master (MIT)</option>
            <option value="T">T — Register CoF token (CIT subsequent)</option>
            <option value="S">S — Register CoF token + 3DS stored card</option>
          </select>
          <div class="hint">After payment GP Webpay returns TOKEN in callback</div>
        </div>
        <div class="field">
          <label>Description (optional)</label>
          <input type="text" name="description" value="">
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Language (optional)</label>
          <input type="text" name="lang" value="" maxlength="2" placeholder="CS / EN / SK">
          <div class="hint">Overrides the config default</div>
        </div>
        <div class="field">
          <label>MD — merchant data (optional)</label>
          <input type="text" name="md" value="" placeholder="any string, max 255 chars">
          <div class="hint">Passed through the payment and returned in callback</div>
        </div>
        <div class="field">
          <label>Customer e-mail (optional)</label>
          <input type="email" name="email" value="">
        </div>
      </div>
      <details style="margin-bottom:.875rem;border:1px solid #e2e8f0;border-radius:6px;overflow:hidden">
        <summary style="padding:.55rem .875rem;background:#f8fafc;cursor:pointer;font-size:.8rem;font-weight:600;color:#475569;user-select:none">
          ADDINFO / PSD2 additional data (optional) — <span style="font-weight:normal">uses POST redirect when filled</span>
        </summary>
        <div style="padding:.875rem;display:flex;flex-direction:column;gap:.75rem">
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Cardholder details</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="addinfo_ch_name" value="">
            </div>
            <div class="field">
              <label>E-mail (in addInfo XML)</label>
              <input type="email" name="addinfo_ch_email" value="">
              <div class="hint">Goes into &lt;cardholderDetails&gt; — separate from top-level EMAIL</div>
            </div>
            <div class="field" style="display:flex;gap:.5rem;align-items:flex-end">
              <div style="flex:0 0 80px">
                <label>Phone country</label>
                <input type="text" name="addinfo_ch_phone_country" value="" placeholder="421">
              </div>
              <div style="flex:1">
                <label>Phone number</label>
                <input type="text" name="addinfo_ch_phone" value="" placeholder="912345678">
              </div>
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Billing address</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="addinfo_billing_name" value="">
            </div>
            <div class="field">
              <label>Address line 1</label>
              <input type="text" name="addinfo_billing_address1" value="">
            </div>
            <div class="field">
              <label>City</label>
              <input type="text" name="addinfo_billing_city" value="">
            </div>
          </div>
          <div class="row3">
            <div class="field">
              <label>ZIP / Postal code</label>
              <input type="text" name="addinfo_billing_postalCode" value="">
            </div>
            <div class="field">
              <label>Country</label>
              <?= countrySelect('addinfo_billing_country') ?>
            </div>
            <div class="field">
              <label>Phone</label>
              <input type="text" name="addinfo_billing_phone" value="" placeholder="+421912345678">
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Shipping address</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="addinfo_shipping_name" value="">
            </div>
            <div class="field">
              <label>Address line 1</label>
              <input type="text" name="addinfo_shipping_address1" value="">
            </div>
            <div class="field">
              <label>City</label>
              <input type="text" name="addinfo_shipping_city" value="">
            </div>
          </div>
          <div class="row3">
            <div class="field">
              <label>ZIP / Postal code</label>
              <input type="text" name="addinfo_shipping_postalCode" value="">
            </div>
            <div class="field">
              <label>Country</label>
              <?= countrySelect('addinfo_shipping_country') ?>
            </div>
            <div class="field">
              <label>Phone</label>
              <input type="text" name="addinfo_shipping_phone" value="" placeholder="+421912345678">
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Payment info</div>
          <div class="row2">
            <div class="field">
              <label>Transaction type</label>
              <select name="addinfo_tx_type">
                <option value="">— none —</option>
                <option value="01">01 — Goods / service</option>
                <option value="02">02 — Check acceptance</option>
                <option value="03">03 — Financing</option>
                <option value="04">04 — Cash advance</option>
                <option value="05">05 — Pre-order</option>
              </select>
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Shopping cart item (optional)</div>
          <div class="row3">
            <div class="field">
              <label>Description</label>
              <input type="text" name="addinfo_item_desc" value="">
            </div>
            <div class="field">
              <label>Quantity</label>
              <input type="number" name="addinfo_item_qty" value="" min="1">
            </div>
            <div class="field">
              <label>Unit price (major units)</label>
              <input type="text" name="addinfo_item_price" value="" placeholder="e.g. 10.00">
            </div>
          </div>
        </div>
      </details>
      <div class="hint" style="margin-bottom:.6rem">Return URL: <code><?= esc($_SESSION['cfg']['return_url'] ?? 'http://localhost:8080/return') ?></code></div>
      <button type="submit" class="btn btn-success">→ Redirect to GP Webpay (HTTP API)</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Card Verification — CoF / Recurring Registration <span style="font-size:.72rem;font-weight:normal;color:#6b7280">— HTTP API, CARD_VERIFICATION operation</span></div>
  <div class="card-body">
    <p style="font-size:.83rem;color:#475569;margin-bottom:.75rem">
      Verifies card validity <strong>without blocking funds</strong>. This is the correct way to register a card for Card-on-File (CoF) or recurring (MIT) payments.
      Use <code>CardPaymentRequest</code> (above) only for regular payments — USERPARAM1 for token/recurring registration must go with CARD_VERIFICATION.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="create_card_verification">
      <div class="row2">
        <div class="field">
          <label>Order number</label>
          <input type="number" name="cv_order_number" value="<?= date('YmdHis') ?>" required min="1">
          <div class="hint">Numeric, returned in callback as ORDERNUMBER</div>
        </div>
        <div class="field">
          <label>USERPARAM1 — registration type <span style="color:#dc2626">*</span></label>
          <select name="cv_userparam1" required>
            <option value="T">T — Register CoF token (CIT — customer-initiated subsequent)</option>
            <option value="S">S — Register CoF token + 3DS stored card (no CVV on reuse)</option>
            <option value="R">R — Register recurring master (MIT — merchant-initiated)</option>
          </select>
          <div class="hint">GP Webpay returns TOKEN in callback after successful card verification</div>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Description (optional)</label>
          <input type="text" name="cv_description" value="">
        </div>
        <div class="field">
          <label>Language (optional)</label>
          <input type="text" name="cv_lang" value="" maxlength="2" placeholder="CS / EN / SK">
        </div>
        <div class="field">
          <label>MD — merchant data (optional)</label>
          <input type="text" name="cv_md" value="" placeholder="any string, max 255 chars">
          <div class="hint">Passed through and returned in callback</div>
        </div>
      </div>
      <div class="row2">
        <div class="field">
          <label>Customer e-mail (optional)</label>
          <input type="email" name="cv_email" value="">
        </div>
      </div>
      <details style="margin-bottom:.875rem;border:1px solid #e2e8f0;border-radius:6px;overflow:hidden">
        <summary style="padding:.55rem .875rem;background:#f8fafc;cursor:pointer;font-size:.8rem;font-weight:600;color:#475569;user-select:none">
          ADDINFO / PSD2 additional data (optional) — <span style="font-weight:normal">uses POST redirect when filled</span>
        </summary>
        <div style="padding:.875rem;display:flex;flex-direction:column;gap:.75rem">
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Cardholder details</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="cv_addinfo_ch_name" value="">
            </div>
            <div class="field">
              <label>E-mail (in addInfo XML)</label>
              <input type="email" name="cv_addinfo_ch_email" value="">
            </div>
            <div class="field" style="display:flex;gap:.5rem;align-items:flex-end">
              <div style="flex:0 0 80px">
                <label>Phone country</label>
                <input type="text" name="cv_addinfo_ch_phone_country" value="" placeholder="421">
              </div>
              <div style="flex:1">
                <label>Phone number</label>
                <input type="text" name="cv_addinfo_ch_phone" value="" placeholder="912345678">
              </div>
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Billing address</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="cv_addinfo_billing_name" value="">
            </div>
            <div class="field">
              <label>Address line 1</label>
              <input type="text" name="cv_addinfo_billing_address1" value="">
            </div>
            <div class="field">
              <label>City</label>
              <input type="text" name="cv_addinfo_billing_city" value="">
            </div>
          </div>
          <div class="row3">
            <div class="field">
              <label>ZIP / Postal code</label>
              <input type="text" name="cv_addinfo_billing_postalCode" value="">
            </div>
            <div class="field">
              <label>Country</label>
              <?= countrySelect('cv_addinfo_billing_country') ?>
            </div>
            <div class="field">
              <label>Phone</label>
              <input type="text" name="cv_addinfo_billing_phone" value="" placeholder="+421912345678">
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Shipping address</div>
          <div class="row3">
            <div class="field">
              <label>Name</label>
              <input type="text" name="cv_addinfo_shipping_name" value="">
            </div>
            <div class="field">
              <label>Address line 1</label>
              <input type="text" name="cv_addinfo_shipping_address1" value="">
            </div>
            <div class="field">
              <label>City</label>
              <input type="text" name="cv_addinfo_shipping_city" value="">
            </div>
          </div>
          <div class="row3">
            <div class="field">
              <label>ZIP / Postal code</label>
              <input type="text" name="cv_addinfo_shipping_postalCode" value="">
            </div>
            <div class="field">
              <label>Country</label>
              <?= countrySelect('cv_addinfo_shipping_country') ?>
            </div>
            <div class="field">
              <label>Phone</label>
              <input type="text" name="cv_addinfo_shipping_phone" value="" placeholder="+421912345678">
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Payment info</div>
          <div class="row2">
            <div class="field">
              <label>Transaction type</label>
              <select name="cv_addinfo_tx_type">
                <option value="">— none —</option>
                <option value="01">01 — Goods / service</option>
                <option value="02">02 — Check acceptance</option>
                <option value="03">03 — Financing</option>
                <option value="04">04 — Cash advance</option>
                <option value="05">05 — Pre-order</option>
              </select>
            </div>
          </div>
          <div style="font-size:.78rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Shopping cart item (optional)</div>
          <div class="row3">
            <div class="field">
              <label>Description</label>
              <input type="text" name="cv_addinfo_item_desc" value="">
            </div>
            <div class="field">
              <label>Quantity</label>
              <input type="number" name="cv_addinfo_item_qty" value="" min="1">
            </div>
            <div class="field">
              <label>Unit price (major units)</label>
              <input type="text" name="cv_addinfo_item_price" value="" placeholder="e.g. 10.00">
            </div>
          </div>
        </div>
      </details>
      <div class="hint" style="margin-bottom:.6rem">Return URL: <code><?= esc($_SESSION['cfg']['return_url'] ?? 'http://localhost:8080/return') ?></code></div>
      <button type="submit" class="btn btn-success">→ Redirect to GP Webpay (Card Verification)</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">New Payment Link <span style="font-size:.72rem;font-weight:normal;color:#6b7280">— SOAP WS API</span></div>
  <div class="card-body">
    <form method="post">
      <input type="hidden" name="action" value="create_link">
      <div class="row2">
        <div class="field">
          <label>Payment number</label>
          <input type="text" name="payment_number" value="<?= date('YmdHis') ?>" required>
        </div>
        <div class="field">
          <label>Order number</label>
          <input type="text" name="order_number" value="<?= date('YmdHis1') ?>" required>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Amount</label>
          <input type="text" name="amount" value="10.00" required>
          <div class="hint">In major currency units (e.g. 10.50)</div>
        </div>
        <div class="field">
          <label>Currency</label>
          <select name="currency_code">
            <?php foreach ($currencies as $code => $label): ?>
              <option value="<?= $code ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>Payment type</label>
          <select name="capture_flag">
            <option value="1">Direct capture (immediate charge)</option>
            <option value="0">Pre-authorisation (capture later)</option>
          </select>
        </div>
        <div class="field">
          <label>Registration</label>
          <select name="registration">
            <option value="">— none —</option>
            <option value="recurring">Recurring</option>
            <option value="token">Token (CoF)</option>
          </select>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Payment expiry <span style="color:#dc2626">*</span></label>
          <input type="text" name="payment_expiry" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
          <div class="hint">YYYY-MM-DD — required</div>
        </div>
        <div class="field">
          <label>Description (optional)</label>
          <input type="text" name="description" value="">
        </div>
        <div class="field">
          <label>Customer e-mail (optional)</label>
          <input type="email" name="email" value="">
        </div>
        <div class="field">
          <label>Reference number (optional)</label>
          <input type="text" name="reference_number" value="">
          <div class="hint">Merchant reference, returned in callback</div>
        </div>
        <div class="field">
          <label>Merchant data (optional)</label>
          <input type="text" name="merchant_data" value="">
          <div class="hint">Arbitrary data passed through to the return URL</div>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Language (optional)</label>
          <input type="text" name="language" value="" maxlength="2" placeholder="e.g. cs, en">
          <div class="hint">2-letter ISO 639-1 code</div>
        </div>
        <div class="field">
          <label>Merchant e-mail (optional)</label>
          <input type="email" name="merchant_email" value="">
          <div class="hint">Notification email for merchant</div>
        </div>
        <div class="field">
          <label>Default pay method (optional)</label>
          <input type="text" name="default_pay_method" value="" placeholder="e.g. CRD">
        </div>
      </div>
      <div class="row2">
        <div class="field">
          <label>Disabled pay methods (optional)</label>
          <input type="text" name="disabled_pay_methods" value="" placeholder="e.g. CSH">
        </div>
        <div class="field">
          <label>Pay methods (optional)</label>
          <input type="text" name="pay_methods" value="" placeholder="e.g. CRD,CSH">
          <div class="hint">Comma-separated list of allowed payment methods</div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Create payment link</button>
    </form>
  </div>
</div>

<?php

// ── Eligible parent payments for dropdowns ─────────────────────────────────
$tokenPayments = $pdo->query(
    "SELECT id, payment_number, token_data, amount, currency_code, type, created_at
     FROM payments WHERE token_data IS NOT NULL AND token_data != '' AND parent_payment_id IS NULL
     ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$recurringMasters = $pdo->query(
    "SELECT id, payment_number, master_payment_number, token_data, amount, currency_code, type, created_at
     FROM payments WHERE registered_for = 'recurring' AND parent_payment_id IS NULL
     ORDER BY id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if ($tokenPayments): ?>
<div class="card">
  <div class="card-header">Tokenized Payment (Card-on-File) <span style="font-size:.72rem;font-weight:normal;color:#6b7280">— SOAP WS API, stored token</span></div>
  <div class="card-body">
    <p style="font-size:.83rem;color:#475569;margin-bottom:.75rem">
      Payment using a stored CoF token. Select the source payment from which the token was obtained.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="create_cof">
      <div class="row3">
        <div class="field" style="grid-column:1/-1">
          <label>Source payment with token <span style="color:#dc2626">*</span></label>
          <select name="parent_id" required>
            <option value="">— select payment —</option>
            <?php foreach ($tokenPayments as $tp): ?>
              <option value="<?= $tp['id'] ?>">
                #<?= $tp['id'] ?> — <?= esc($tp['payment_number']) ?>
                (<?= esc($tp['type']) ?>)
                token: <?= esc(substr($tp['token_data'], 0, 20)) ?>…
                <?= esc(substr($tp['created_at'], 0, 10)) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="hint">Only payments with a stored token in the database</div>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Amount</label>
          <input type="text" name="amount" value="10.00" required>
          <div class="hint">In major units (e.g. 10.50)</div>
        </div>
        <div class="field">
          <label>Currency</label>
          <select name="currency_code">
            <?php foreach ($currencies as $code => $label): ?>
              <option value="<?= $code ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>Deposit flag</label>
          <select name="deposit_flag">
            <option value="1">1 — Direct capture (immediate charge)</option>
            <option value="0">0 — Pre-auth (capture later)</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">💳 Run CoF payment</button>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($recurringMasters): ?>
<div class="card">
  <div class="card-header">Recurring Payment <span style="font-size:.72rem;font-weight:normal;color:#6b7280">— SOAP WS API, MIT</span></div>
  <div class="card-body">
    <p style="font-size:.83rem;color:#475569;margin-bottom:.75rem">
      Merchant-initiated payment against an existing master. For HTTP master payments the TOKEN from the callback is used as <code>masterPaymentNumber</code>.
    </p>
    <form method="post">
      <input type="hidden" name="action" value="create_recurring">
      <div class="row3">
        <div class="field" style="grid-column:1/-1">
          <label>Master payment <span style="color:#dc2626">*</span></label>
          <select name="parent_id" required>
            <option value="">— select master payment —</option>
            <?php foreach ($recurringMasters as $rm): ?>
              <?php
                $hasMpn = !empty($rm['token_data']) || !empty($rm['master_payment_number']);
                $mpnInfo = $rm['token_data']
                    ? 'token: ' . substr($rm['token_data'], 0, 16) . '…'
                    : ($rm['master_payment_number'] ? 'mpn: ' . $rm['master_payment_number'] : 'pn: ' . $rm['payment_number']);
              ?>
              <option value="<?= $rm['id'] ?>">
                #<?= $rm['id'] ?> — <?= esc($rm['payment_number']) ?>
                (<?= esc($rm['type']) ?>)
                <?= esc($mpnInfo) ?>
                <?= esc(substr($rm['created_at'], 0, 10)) ?>
                <?= !$hasMpn ? ' ⚠ no token yet' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="hint">Only payments registered for recurring. Payments without token/mpn have not been completed yet.</div>
        </div>
      </div>
      <div class="row3">
        <div class="field">
          <label>Amount</label>
          <input type="text" name="amount" value="10.00" required>
          <div class="hint">In major units (e.g. 10.50)</div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">↻ Run recurring payment</button>
    </form>
  </div>
</div>
<?php endif; ?>

<?php endif; ?>

<?php declare(strict_types=1); ?>
<div class="card">
  <div class="card-header">Merchant Configuration</div>
  <div class="card-body">
    <form method="post">
      <input type="hidden" name="action" value="save_config">

      <div class="row2">
        <div class="field">
          <label>Provider</label>
          <select name="provider" required>
            <?php foreach ($providers as $code => $label): ?>
              <option value="<?= esc($code) ?>" <?= cfgVal('provider', '0300') === esc($code) ? 'selected' : '' ?>>
                <?= esc($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>Merchant number</label>
          <input type="text" name="merchant_number" value="<?= cfgVal('merchant_number') ?>" required>
        </div>
      </div>

      <div class="field">
        <label>Merchant private key (PEM) *</label>
        <textarea name="merchant_private_key" rows="8"
          placeholder="-----BEGIN RSA PRIVATE KEY-----&#10;...&#10;-----END RSA PRIVATE KEY-----" required><?= cfgVal('merchant_private_key') ?></textarea>
      </div>

      <div class="row2">
        <div class="field">
          <label>Key password (if protected)</label>
          <input type="text" name="merchant_key_password" value="<?= cfgVal('merchant_key_password') ?>">
        </div>
        <div class="field">
          <label>Return URL</label>
          <input type="text" name="return_url" value="<?= cfgVal('return_url', 'http://localhost:8080/return') ?>" required>
          <div class="hint">Redirect URL after payment</div>
        </div>
      </div>

      <div class="field">
        <label>GPE public key (PEM) — leave empty to use the bundled test key</label>
        <textarea name="gpe_public_key" rows="4"
          placeholder="Leave empty → uses certs/gpe.signing_test.pem"><?= cfgVal('gpe_public_key') ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Save configuration</button>
    </form>
  </div>
</div>

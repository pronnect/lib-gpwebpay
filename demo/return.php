<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Pronnect\GpWebPay\ReturnUrlVerifier;

// ─── DB ───────────────────────────────────────────────────────────────────────

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$pdo = new PDO('sqlite:' . $dataDir . '/demo.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ─── Parse params ─────────────────────────────────────────────────────────────

// Pass ALL query params to the verifier — the digest covers up to 19 fields and
// omitting any field that GP Webpay included would cause a signature mismatch.
$p = array_map('strval', $_GET);

// ─── Signature verification (DIGEST / DIGEST1 includes merchantNumber) ────────

$signatureValid = null;
$verifyError    = null;
$cfg            = $_SESSION['cfg'] ?? null;

if ($cfg && !empty($p['DIGEST1'])) {
    try {
        $gpeKey   = trim($cfg['gpe_public_key'])
            ?: file_get_contents(__DIR__ . '/../certs/gpe.signing_test.pem');
        $verifier = new ReturnUrlVerifier(
            $gpeKey,
            $cfg['merchant_number'],
            $cfg['merchant_private_key'],
            $cfg['merchant_key_password'] ?: null,
        );
        $signatureValid = $verifier->verify($p);
    } catch (Throwable $e) {
        $signatureValid = false;
        $verifyError    = $e->getMessage();
    }
}

// ─── Load code descriptions ───────────────────────────────────────────────────

function resolveCode(string $xpath, string $value): string
{
    static $dom = null;
    if ($dom === null) {
        $xml = @file_get_contents(__DIR__ . '/../resources/xml/codes.xml');
        if ($xml) {
            $doc = new DOMDocument();
            if (@$doc->loadXML($xml)) {
                $dom = new DOMXPath($doc);
            }
        }
    }
    if ($dom === null) return '';
    $nodes = $dom->query(sprintf($xpath, $value));
    return $nodes->count() ? trim($nodes->item(0)->textContent) : '';
}

$prDesc = resolveCode('/codes/primaryReturnCodes/code[@value="%s"]',   $p['PRCODE']  ?? '');
$srDesc = resolveCode('/codes/secondaryReturnCode/code[@value="%s"]',  $p['SRCODE']  ?? '');

// ─── Find & update payment in DB ──────────────────────────────────────────────

$payment = null;
if ($p['ORDERNUMBER']) {
    $st = $pdo->prepare(
        'SELECT * FROM payments WHERE payment_number = ? OR order_number = ? ORDER BY id DESC LIMIT 1'
    );
    $st->execute([$p['ORDERNUMBER'], $p['ORDERNUMBER']]);
    $payment = $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($payment && $signatureValid) {
    $newStatus  = ($p['PRCODE'] ?? '') === '0' ? 'OK' : 'FAILED_' . ($p['PRCODE'] ?? '?');
    $tokenData  = trim($p['TOKEN'] ?? '');   // GP Webpay returns TOKEN after CoF/recurring reg.

    // Build UPDATE dynamically — only set token_data when GP Webpay returned one
    if ($tokenData !== '') {
        $pdo->prepare(
            'UPDATE payments SET last_status = ?, token_data = ?, last_api_response = ? WHERE id = ?'
        )->execute([
            $newStatus,
            $tokenData,
            json_encode($p, JSON_UNESCAPED_UNICODE),
            $payment['id'],
        ]);
    } else {
        $pdo->prepare(
            'UPDATE payments SET last_status = ?, last_api_response = ? WHERE id = ?'
        )->execute([
            $newStatus,
            json_encode($p, JSON_UNESCAPED_UNICODE),
            $payment['id'],
        ]);
    }
}

// ─── HTML ─────────────────────────────────────────────────────────────────────

function esc(mixed $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$ok      = $p['PRCODE'] === '0';
$sigIcon = match ($signatureValid) {
    true  => '<span style="color:#16a34a">✓ valid</span>',
    false => '<span style="color:#dc2626">✕ INVALID</span>',
    null  => '<span style="color:#d97706">— not verified (config missing)</span>',
};
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GP Webpay — Payment result</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,sans-serif;background:#f0f2f5;color:#1a1a2e;font-size:14px;display:flex;align-items:flex-start;justify-content:center;padding:2rem 1rem}
.card{background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1);width:100%;max-width:640px}
.card-header{padding:.75rem 1.25rem;border-bottom:1px solid #e2e8f0;font-weight:600;font-size:.9rem}
.card-header.ok  {background:#ecfdf5;color:#065f46}
.card-header.fail{background:#fef2f2;color:#991b1b}
.card-body{padding:1.25rem}
.dl{display:grid;grid-template-columns:180px 1fr;gap:.45rem .75rem;align-items:start}
.dl dt{font-size:.78rem;color:#6b7280;font-weight:600}
.dl dd{font-size:.875rem;word-break:break-all}
.badge{display:inline-block;padding:.15rem .6rem;border-radius:99px;font-size:.75rem;font-weight:700}
.badge-ok  {background:#d1fae5;color:#065f46}
.badge-fail{background:#fee2e2;color:#991b1b}
.actions{margin-top:1.25rem;display:flex;gap:.5rem;flex-wrap:wrap}
.btn{display:inline-block;padding:.4rem 1rem;border:none;border-radius:5px;font:inherit;font-size:.85rem;cursor:pointer;font-weight:500;text-decoration:none}
.btn-primary{background:#2563eb;color:#fff}
.btn-neutral{background:#475569;color:#fff}
pre{background:#1e293b;color:#e2e8f0;border-radius:6px;padding:.75rem 1rem;font-size:.75rem;white-space:pre-wrap;word-break:break-all;margin-top:.75rem;overflow:auto;max-height:180px}
</style>
</head>
<body>
<div class="card">
  <div class="card-header <?= $ok ? 'ok' : 'fail' ?>">
    <?= $ok ? '✓ Payment successful' : '✕ Payment failed' ?>
    &nbsp;<span class="badge <?= $ok ? 'badge-ok' : 'badge-fail' ?>"><?= esc($p['RESULTTEXT'] ?? '—') ?></span>
  </div>
  <div class="card-body">
    <dl class="dl">
      <dt>Operation</dt>        <dd><?= esc($p['OPERATION'] ?? '—') ?></dd>
      <dt>Payment number</dt>  <dd><code><?= esc($p['ORDERNUMBER'] ?? '—') ?></code></dd>
      <dt>Order number</dt>    <dd><code><?= esc($p['MERORDERNUM'] ?? '—') ?></code></dd>
      <dt>Primary code</dt>    <dd>
        <strong><?= esc($p['PRCODE'] ?? '—') ?></strong>
        <?php if ($prDesc): ?> — <?= esc($prDesc) ?><?php endif; ?>
      </dd>
      <dt>Secondary code</dt>  <dd>
        <strong><?= esc($p['SRCODE'] ?? '—') ?></strong>
        <?php if ($srDesc): ?> — <?= esc($srDesc) ?><?php endif; ?>
      </dd>
      <dt>Signature (DIGEST1)</dt><dd><?= $sigIcon ?></dd>
      <?php if ($verifyError): ?>
      <dt>Verification error</dt><dd style="color:#dc2626"><?= esc($verifyError) ?></dd>
      <?php endif; ?>
      <?php if ($payment): ?>
      <dt>Payment in DB</dt>     <dd><a href="/?page=detail&id=<?= $payment['id'] ?>">ID <?= $payment['id'] ?> →</a></dd>
      <?php endif; ?>
    </dl>

    <pre><?= esc(json_encode(array_diff_key($p, array_flip(['DIGEST','DIGEST1'])), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>

    <div class="actions">
      <?php if ($payment): ?>
        <a href="/?page=detail&id=<?= $payment['id'] ?>" class="btn btn-primary">Payment detail →</a>
      <?php endif; ?>
      <a href="/?page=list" class="btn btn-neutral">Payment list</a>
    </div>
  </div>
</div>
</body>
</html>

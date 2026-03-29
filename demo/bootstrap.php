<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pronnect\GpWebPay\Config;
use Pronnect\GpWebPay\DigestSigner;
use Pronnect\GpWebPay\Gateway;
use Pronnect\GpWebPay\Http\HttpConfig;
use Pronnect\GpWebPay\Http\HttpGateway;

// ─── Helpers ──────────────────────────────────────────────────────────────────

function esc(mixed $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function money(int $cents, int $currency = 978): string
{
    $sym = match ($currency) { 203 => 'CZK', 840 => 'USD', 348 => 'HUF', default => 'EUR' };
    return number_format($cents / 100, 2, '.', ' ') . ' ' . $sym;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function popFlash(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function hasConfig(): bool
{
    return !empty($_SESSION['cfg']['merchant_private_key']);
}

function makeGateway(): Gateway
{
    $cfg    = $_SESSION['cfg'];
    $gpeKey = trim($cfg['gpe_public_key']) ?: file_get_contents(__DIR__ . '/../certs/gpe.signing_test.pem');
    $config = new Config([
        'isTestEnvironment'          => true,
        'provider'                   => $cfg['provider'],
        'merchantNumber'             => $cfg['merchant_number'],
        'GPEPublicKey'               => $gpeKey,
        'merchantPrivateKey'         => $cfg['merchant_private_key'],
        'merchantPrivateKeyPassword' => $cfg['merchant_key_password'] ?: null,
        'wsClientOptions'            => ['trace' => true],
    ]);
    $signer = new DigestSigner(
        $config->getGPEPublicKey(),
        $config->getMerchantPrivateKey(),
        $config->getMerchantPrivateKeyPassword()
    );

    $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context%\n", null, true, true);
    $handler   = new StreamHandler('php://stdout', Logger::DEBUG);
    $handler->setFormatter($formatter);
    $logger = new Logger('gpwebpay');
    $logger->pushHandler($handler);

    return new Gateway($config, $signer, $logger);
}

function makeHttpGateway(): HttpGateway
{
    $cfg    = $_SESSION['cfg'];
    $gpeKey = trim($cfg['gpe_public_key']) ?: file_get_contents(__DIR__ . '/../certs/gpe.signing_test.pem');
    $config = new HttpConfig([
        'isTestEnvironment'          => true,
        'merchantNumber'             => $cfg['merchant_number'],
        'GPEPublicKey'               => $gpeKey,
        'merchantPrivateKey'         => $cfg['merchant_private_key'],
        'merchantPrivateKeyPassword' => $cfg['merchant_key_password'] ?: null,
    ]);
    $rawSigner = new DigestSigner(
        $config->getGPEPublicKey(),
        $config->getMerchantPrivateKey(),
        $config->getMerchantPrivateKeyPassword(),
    );
    return HttpGateway::create($config, $rawSigner);
}

function amountCents(string $raw): int
{
    return (int)round((float)str_replace(',', '.', $raw) * 100);
}

function gpwpError(Throwable $e): string
{
    if ($e instanceof SoapFault && isset($e->detail->serviceException)) {
        /** @var \Pronnect\GpWebPay\ServiceException $se */
        $se  = $e->detail->serviceException;
        $pri = $se->getPrimaryReturnCode();
        $msg = sprintf('[%s] %s', $pri, $se->getMessage());

        $sec = $se->getSecondaryReturnCode();
        if ($sec !== null && $sec !== '') {
            static $secCodes = null;
            if ($secCodes === null) {
                $secCodes = [];
                $xml = @file_get_contents(__DIR__ . '/../resources/xml/codes.xml');
                if ($xml) {
                    $dom = new DOMDocument();
                    if (@$dom->loadXML($xml)) {
                        $xpath = new DOMXPath($dom);
                        foreach ($xpath->query('/codes/secondaryReturnCode/code') as $node) {
                            $secCodes[$node->getAttribute('value')] = trim($node->textContent);
                        }
                    }
                }
            }
            $secLabel = $secCodes[$sec] ?? '';
            $msg .= ' | secondary: [' . $sec . ']' . ($secLabel !== '' ? ' ' . $secLabel : '');
        }

        return $msg;
    }

    return $e->getMessage();
}

function paymentRow(PDO $pdo, int $id): ?array
{
    $st = $pdo->prepare('SELECT * FROM payments WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function updatePayment(PDO $pdo, int $id, array $data): void
{
    $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
    $st   = $pdo->prepare("UPDATE payments SET $sets WHERE id = :id");
    $data['id'] = $id;
    $st->execute($data);
}

function flashSoapLog(Gateway $gw): void
{
    $_SESSION['last_soap'] = [
        'req'  => $gw->getLastRequest(),
        'resp' => $gw->getLastResponse(),
    ];
}

function emitPostRedirect(string $url, array $params): never
{
    $esc = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

    // Pretty-print ADDINFO XML for display
    $addInfoFormatted = '';
    if (isset($params['ADDINFO']) && $params['ADDINFO'] !== '') {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        if (@$dom->loadXML($params['ADDINFO'])) {
            $addInfoFormatted = $dom->saveXML() ?: $params['ADDINFO'];
        } else {
            $addInfoFormatted = $params['ADDINFO'];
        }
    }

    $rows = '';
    foreach ($params as $name => $value) {
        if ($name === 'ADDINFO') {
            continue; // rendered separately below
        }
        $display = (strlen($value) > 80) ? substr($value, 0, 80) . '…' : $value;
        $rows .= '<tr><td style="padding:.35rem .75rem;border-bottom:1px solid #f1f5f9;font-family:monospace;font-size:.8rem;color:#374151;white-space:nowrap">'
            . $esc($name) . '</td>'
            . '<td style="padding:.35rem .75rem;border-bottom:1px solid #f1f5f9;font-family:monospace;font-size:.78rem;color:#6b7280;word-break:break-all">'
            . $esc($display) . '</td></tr>';
    }

    // All params except ADDINFO go as regular hidden inputs.
    // ADDINFO is set via JavaScript to bypass HTML-attribute decoding by the browser.
    $hiddenInputs = '';
    $addInfoJs    = '';
    foreach ($params as $name => $value) {
        if ($name === 'ADDINFO') {
            $hiddenInputs .= '<input type="hidden" name="ADDINFO" value='. "'".htmlentities($value, ENT_QUOTES)."'>";
        } else {
            $hiddenInputs .= '<input type="hidden"'
                . ' name="'  . htmlspecialchars($name,  ENT_QUOTES, 'UTF-8') . '"'
                . ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    $addInfoBlock = '';
    if ($addInfoFormatted !== '') {
        $addInfoBlock = '<div style="margin-bottom:1.25rem">'
            . '<div style="font-size:.72rem;font-weight:700;color:#6b7280;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase">ADDINFO</div>'
            . '<pre style="background:#1e293b;color:#e2e8f0;border-radius:6px;padding:.75rem 1rem;font-family:monospace;font-size:.78rem;white-space:pre;overflow-x:auto;line-height:1.5">'
            . $esc($addInfoFormatted)
            . '</pre></div>';
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GP Webpay — POST redirect</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,sans-serif;background:#f0f2f5;color:#1a1a2e;font-size:14px;display:flex;justify-content:center;padding:2rem 1rem}
.card{background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1);width:100%;max-width:700px}
.card-header{padding:.75rem 1.25rem;background:#eff6ff;border-bottom:1px solid #bfdbfe;font-weight:600;font-size:.875rem;color:#1e40af}
.card-body{padding:1.25rem}
table{width:100%;border-collapse:collapse}
th{padding:.4rem .75rem;text-align:left;font-size:.72rem;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;background:#f8fafc;border-bottom:1px solid #e2e8f0}
.btn{display:inline-block;padding:.5rem 1.5rem;border:none;border-radius:5px;font:inherit;font-size:.9rem;cursor:pointer;font-weight:600;background:#16a34a;color:#fff;transition:background .15s}
.btn:hover{background:#15803d}
.url{font-family:monospace;font-size:.8rem;color:#475569;word-break:break-all;margin-bottom:1rem;padding:.5rem .75rem;background:#f8fafc;border-radius:5px;border:1px solid #e2e8f0}
</style>
</head>
<body>
<div class="card">
  <div class="card-header">GP Webpay — POST redirect with ADDINFO</div>
  <div class="card-body">
    <p style="font-size:.83rem;color:#475569;margin-bottom:.75rem">
      ADDINFO requires a POST form submission. Review the parameters below and click the button to proceed to the payment gateway.
    </p>
    <div class="url">{$esc($url)}</div>
    <table style="margin-bottom:1.25rem">
      <thead><tr><th>Field</th><th>Value</th></tr></thead>
      <tbody>{$rows}</tbody>
    </table>
    {$addInfoBlock}
    <form method="post" action="{$esc($url)}">
      {$hiddenInputs}
      <button type="submit" class="btn">→ Proceed to GP Webpay</button>
    </form>
    <script>{$addInfoJs}</script>
  </div>
</div>
</body>
</html>
HTML;
    exit;
}

function popSoapLog(): ?array
{
    $log = $_SESSION['last_soap'] ?? null;
    unset($_SESSION['last_soap']);
    return $log;
}

function formatXml(?string $xml): string
{
    if ($xml === null || $xml === '') return '';
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput       = true;
    return @$dom->loadXML($xml) ? ($dom->saveXML() ?: $xml) : $xml;
}

function renderFlash(): void
{
    foreach (popFlash() as $f) {
        $cls = $f['type'] === 'ok' ? 'flash-ok' : 'flash-err';
        echo '<div class="' . $cls . '">' . esc($f['msg']) . '</div>';
    }
}

function cfgVal(string $key, string $default = ''): string
{
    return esc($_SESSION['cfg'][$key] ?? $default);
}

function navLink(string $p, string $label, string $currentPage): string
{
    $active = ($currentPage === $p) ? ' nav-active' : '';
    return '<a href="?page=' . $p . '" class="nav-link' . $active . '">' . $label . '</a>';
}

// ─── Lookup lists ─────────────────────────────────────────────────────────────

$providers = [
    '0100' => 'KB SmartPay (0100)',
    '0110' => 'KB SmartPay / Worldline (0110)',
    '0300' => 'ČSOB CZ (0300)',
    '0870' => 'Global Payments RO (0870)',
    '0880' => 'Global Payments CZ (0880)',
    '0902' => 'Global Payments SK (0902)',
    '0910' => 'Global Payments AT (0910)',
    '1111' => 'UniCredit SK (1111)',
    '2702' => 'UniCredit CZ (2702)',
    '5501' => 'EVO Payments (5501)',
    '6500' => 'Poštová banka (6500)',
    '7500' => 'ČSOB SK (7500)',
    '8470' => 'Global Payments Malta (8470)',
    '9203' => 'Global Payments Europe CZ (9203)',
    '9348' => 'Global Payments Europe HU (9348)',
];

$currencies = [978 => 'EUR (978)', 203 => 'CZK (203)', 840 => 'USD (840)', 348 => 'HUF (348)'];

// ISO 3166-1 numeric codes (3-digit, zero-padded)
$isoCountries = [
    '008' => 'Albania (008)',
    '012' => 'Algeria (012)',
    '040' => 'Austria (040)',
    '056' => 'Belgium (056)',
    '070' => 'Bosnia and Herzegovina (070)',
    '100' => 'Bulgaria (100)',
    '112' => 'Belarus (112)',
    '191' => 'Croatia (191)',
    '196' => 'Cyprus (196)',
    '203' => 'Czech Republic (203)',
    '208' => 'Denmark (208)',
    '233' => 'Estonia (233)',
    '246' => 'Finland (246)',
    '250' => 'France (250)',
    '276' => 'Germany (276)',
    '300' => 'Greece (300)',
    '348' => 'Hungary (348)',
    '352' => 'Iceland (352)',
    '372' => 'Ireland (372)',
    '376' => 'Israel (376)',
    '380' => 'Italy (380)',
    '400' => 'Jordan (400)',
    '414' => 'Kuwait (414)',
    '428' => 'Latvia (428)',
    '440' => 'Lithuania (440)',
    '442' => 'Luxembourg (442)',
    '470' => 'Malta (470)',
    '498' => 'Moldova (498)',
    '504' => 'Morocco (504)',
    '528' => 'Netherlands (528)',
    '578' => 'Norway (578)',
    '616' => 'Poland (616)',
    '620' => 'Portugal (620)',
    '642' => 'Romania (642)',
    '688' => 'Serbia (688)',
    '703' => 'Slovakia (703)',
    '705' => 'Slovenia (705)',
    '724' => 'Spain (724)',
    '752' => 'Sweden (752)',
    '756' => 'Switzerland (756)',
    '792' => 'Turkey (792)',
    '804' => 'Ukraine (804)',
    '826' => 'United Kingdom (826)',
    '840' => 'United States (840)',
    '860' => 'Uzbekistan (860)',
    '887' => 'Yemen (887)',
];

function countrySelect(string $name, string $selected = ''): string
{
    global $isoCountries;
    $html = '<select name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" style="width:100%">';
    $html .= '<option value="">— select country —</option>';
    foreach ($isoCountries as $code => $label) {
        $sel   = ($selected === $code) ? ' selected' : '';
        $html .= '<option value="' . $code . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    $html .= '</select>';
    return $html;
}

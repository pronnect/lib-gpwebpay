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

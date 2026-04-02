<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/db.php';
require __DIR__ . '/actions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GP Webpay Demo</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,sans-serif;background:#f0f2f5;color:#1a1a2e;font-size:14px}
a{color:#2563eb;text-decoration:none}
a:hover{text-decoration:underline}

/* layout */
.topbar{background:#1e293b;color:#e2e8f0;padding:.75rem 1.5rem;display:flex;align-items:center;gap:1.5rem}
.topbar-title{font-weight:700;font-size:1rem;color:#fff;margin-right:auto}
.badge-test{background:#f59e0b;color:#000;font-size:.7rem;font-weight:700;padding:.1rem .4rem;border-radius:3px;margin-left:.5rem}
.nav-link{color:#94a3b8;font-size:.875rem;padding:.25rem .5rem;border-radius:4px}
.nav-link:hover{color:#fff;text-decoration:none}
.nav-active{color:#fff;background:#334155}
.content{max-width:900px;margin:1.5rem auto;padding:0 1rem}

/* cards */
.card{background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1);margin-bottom:1.25rem;overflow:hidden}
.card-header{padding:.75rem 1.25rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;font-weight:600;font-size:.875rem;color:#475569}
.card-body{padding:1.25rem}

/* forms */
.field{margin-bottom:.875rem}
label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem}
input[type=text],input[type=email],input[type=number],select,textarea{
  width:100%;padding:.45rem .65rem;border:1px solid #d1d5db;border-radius:5px;font:inherit;font-size:.875rem;
  background:#fff;color:#111;transition:border-color .15s}
input:focus,select:focus,textarea:focus{outline:none;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
textarea{resize:vertical;font-family:monospace;font-size:.78rem}
.hint{font-size:.75rem;color:#6b7280;margin-top:.2rem}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem}

/* buttons */
.btn{display:inline-block;padding:.4rem 1rem;border:none;border-radius:5px;font:inherit;font-size:.85rem;cursor:pointer;font-weight:500;transition:background .15s}
.btn-primary{background:#2563eb;color:#fff}.btn-primary:hover{background:#1d4ed8}
.btn-success{background:#16a34a;color:#fff}.btn-success:hover{background:#15803d}
.btn-warning{background:#d97706;color:#fff}.btn-warning:hover{background:#b45309}
.btn-danger {background:#dc2626;color:#fff}.btn-danger:hover{background:#b91c1c}
.btn-neutral{background:#475569;color:#fff}.btn-neutral:hover{background:#334155}
.btn-sm{padding:.3rem .7rem;font-size:.78rem}

/* flash */
.flash-ok {background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;padding:.65rem 1rem;border-radius:6px;margin-bottom:1rem}
.flash-err{background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:.65rem 1rem;border-radius:6px;margin-bottom:1rem}

/* table */
table{width:100%;border-collapse:collapse}
th,td{padding:.55rem .75rem;text-align:left;border-bottom:1px solid #f1f5f9}
th{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;background:#f8fafc}
tr:last-child td{border-bottom:0}
tr:hover td{background:#f9fafb}

/* detail grid */
.dl{display:grid;grid-template-columns:160px 1fr;gap:.4rem .75rem;align-items:start}
.dl dt{font-size:.78rem;color:#6b7280;font-weight:600;padding-top:.1rem}
.dl dd{font-size:.875rem;word-break:break-all}

/* pill */
.pill{display:inline-block;padding:.1rem .55rem;border-radius:99px;font-size:.72rem;font-weight:700}
.pill-ok   {background:#d1fae5;color:#065f46}
.pill-warn {background:#fef3c7;color:#92400e}
.pill-err  {background:#fee2e2;color:#991b1b}
.pill-gray {background:#f1f5f9;color:#475569}

/* action sections */
.action-section{border:1px solid #e2e8f0;border-radius:6px;padding:1rem;margin-bottom:.75rem}
.action-section h4{font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.6rem}
.inline-form{display:flex;gap:.5rem;align-items:flex-end;flex-wrap:wrap}
.inline-form .field{margin:0}

/* response box */
.resp-box{background:#1e293b;color:#e2e8f0;border-radius:6px;padding:.75rem 1rem;font-family:monospace;font-size:.78rem;white-space:pre-wrap;word-break:break-all;max-height:200px;overflow:auto}
</style>
</head>
<body>

<div class="topbar">
  <span class="topbar-title">GP Webpay Demo <span class="badge-test">TEST</span></span>
  <?= navLink('list',   'Payments',      $page) ?>
  <?= navLink('new',    'New Payment',   $page) ?>
  <?= navLink('config', 'Configuration', $page) ?>
</div>

<div class="content">
<?php renderFlash(); ?>

<?php $soapLog = popSoapLog(); if ($soapLog && ($soapLog['req'] || $soapLog['resp'])): ?>
<details style="margin-bottom:1rem;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.08)">
  <summary style="padding:.65rem 1.25rem;cursor:pointer;background:#f8fafc;font-size:.82rem;font-weight:600;color:#374151;display:flex;justify-content:space-between;align-items:center">
    <span>SOAP request / response</span>
    <span style="font-weight:normal;color:#94a3b8;font-size:.75rem">click to expand</span>
  </summary>
  <div style="padding:.75rem 1.25rem">
    <?php if ($soapLog['req']): ?>
    <div style="margin-bottom:.75rem">
      <div style="font-size:.72rem;font-weight:700;color:#6b7280;letter-spacing:.04em;margin-bottom:.3rem">REQUEST</div>
      <pre class="resp-box" style="max-height:340px"><?= esc(formatXml($soapLog['req'])) ?></pre>
    </div>
    <?php endif; ?>
    <?php if ($soapLog['resp']): ?>
    <div>
      <div style="font-size:.72rem;font-weight:700;color:#6b7280;letter-spacing:.04em;margin-bottom:.3rem">RESPONSE</div>
      <pre class="resp-box" style="max-height:340px"><?= esc(formatXml($soapLog['resp'])) ?></pre>
    </div>
    <?php endif; ?>
  </div>
</details>
<?php endif; ?>

<?php match ($page) {
    'config' => require __DIR__ . '/pages/config.php',
    'new'    => require __DIR__ . '/pages/new.php',
    'list'   => require __DIR__ . '/pages/list.php',
    'detail' => require __DIR__ . '/pages/detail.php',
    default  => require __DIR__ . '/pages/list.php',
}; ?>

</div><!-- /content -->
</body>
</html>

<?php declare(strict_types=1);

$rows = $pdo->query('SELECT * FROM payments ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
  <strong><?= count($rows) ?> records</strong>
  <a href="?page=new" class="btn btn-primary btn-sm">+ New Payment</a>
</div>
<div class="card">
  <?php if (empty($rows)): ?>
    <div class="card-body" style="color:#6b7280">No payments yet. <a href="?page=new">Create the first one.</a></div>
  <?php else: ?>
  <table>
    <thead><tr>
      <th>ID</th><th>Type</th><th>Payment #</th><th>Amount</th>
      <th>Status / State</th><th>Card</th><th>Created</th><th></th>
    </tr></thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
      <?php
        $typeLabel = match($row['type']) {
            'recurring'    => '<span class="pill pill-warn">Recurring</span>',
            'card_on_file' => '<span class="pill pill-ok">CoF</span>',
            'preauth'      => '<span class="pill pill-warn">Pre-auth</span>',
            'http_order'        => '<span class="pill" style="background:#dbeafe;color:#1e40af">HTTP</span>',
            'card_verification' => '<span class="pill" style="background:#f0fdf4;color:#166534">CoF Reg</span>',
            'apple_pay_order'   => '<span class="pill" style="background:#f3f4f6;color:#111827">&#xF8FF; Apple Pay</span>',
            'google_pay_order'  => '<span class="pill" style="background:#fef9c3;color:#854d0e">G Google Pay</span>',
            default        => '<span class="pill pill-gray">SOAP Link</span>',
        };
        $statusPill = $row['last_state']
            ? '<span class="pill pill-ok">' . esc($row['last_state']) . '</span> <span class="pill pill-gray">' . esc($row['last_status'] ?? '') . '</span>'
            : ($row['last_status'] ? '<span class="pill pill-gray">' . esc($row['last_status']) . '</span>' : '—');
        $tokenBadge = !empty($row['token_data']) && empty($row['parent_payment_id'])
            ? '<span class="pill pill-ok" title="' . esc($row['token_data']) . '">🔑 token</span>'
            : '';
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $typeLabel ?> <?= $tokenBadge ?></td>
        <td><code style="font-size:.78rem"><?= esc($row['payment_number']) ?></code></td>
        <td><?= money((int)$row['amount'], (int)$row['currency_code']) ?></td>
        <td><?= $statusPill ?></td>
        <td><?= $row['pan_masked'] ? esc($row['pan_masked']) . ' ' . esc($row['brand_name'] ?? '') : '—' ?></td>
        <td style="color:#6b7280;font-size:.78rem"><?= esc(substr($row['created_at'], 0, 16)) ?></td>
        <td><a href="?page=detail&id=<?= $row['id'] ?>" class="btn btn-neutral btn-sm">Detail →</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

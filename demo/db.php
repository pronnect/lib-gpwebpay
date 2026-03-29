<?php
declare(strict_types=1);

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$pdo = new PDO('sqlite:' . $dataDir . '/demo.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS payments (
    id                    INTEGER PRIMARY KEY AUTOINCREMENT,
    type                  TEXT    NOT NULL DEFAULT 'link',
    registered_for        TEXT,
    payment_number        TEXT    NOT NULL,
    order_number          TEXT,
    amount                INTEGER NOT NULL,
    currency_code         INTEGER NOT NULL DEFAULT 978,
    payment_link          TEXT,
    master_payment_number TEXT,
    token_data            TEXT,
    last_status           TEXT,
    last_state            TEXT,
    last_sub_status       TEXT,
    pan_masked            TEXT,
    brand_name            TEXT,
    auth_code             TEXT,
    created_at            TEXT    NOT NULL DEFAULT (datetime('now')),
    last_api_response     TEXT
)
SQL);

// Migrate existing DB — ignore error if column already exists
try { $pdo->exec('ALTER TABLE payments ADD COLUMN registered_for TEXT'); } catch (\Exception $e) {}
try { $pdo->exec('ALTER TABLE payments ADD COLUMN http_redirect_url TEXT'); } catch (\Exception $e) {}
try { $pdo->exec('ALTER TABLE payments ADD COLUMN parent_payment_id INTEGER'); } catch (\Exception $e) {}
try { $pdo->exec('ALTER TABLE payments ADD COLUMN authentication_link TEXT'); } catch (\Exception $e) {}

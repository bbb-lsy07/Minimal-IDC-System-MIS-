<?php

declare(strict_types=1);

function billing_add_interval(DateTimeImmutable $base, string $cycle): DateTimeImmutable
{
    return match ($cycle) {
        'month' => $base->add(new DateInterval('P1M')),
        'quarter' => $base->add(new DateInterval('P3M')),
        'year' => $base->add(new DateInterval('P1Y')),
        default => throw new InvalidArgumentException('Invalid billing cycle'),
    };
}

function billing_expire_services(): int
{
    return db_exec(
        "UPDATE services SET status = 'suspended'\n         WHERE status = 'active' AND expire_at IS NOT NULL AND expire_at < NOW()"
    );
}

function billing_charge_metered_services(): int
{
    $services = db_fetch_all(
        "SELECT s.*, u.balance AS user_balance\n         FROM services s\n         JOIN users u ON u.id = s.user_id\n         WHERE s.status = 'active' AND s.unit_price_per_second IS NOT NULL AND s.last_billed_at IS NOT NULL"
    );

    $charged = 0;
    foreach ($services as $svc) {
        $serviceId = (int)$svc['id'];
        $userId = (int)$svc['user_id'];
        $price = (float)$svc['unit_price_per_second'];
        $last = new DateTimeImmutable((string)$svc['last_billed_at']);
        $now = new DateTimeImmutable('now');
        $seconds = max(0, $now->getTimestamp() - $last->getTimestamp());
        if ($seconds <= 0) {
            continue;
        }

        $fee = round($seconds * $price, 6);
        if ($fee <= 0) {
            db_exec('UPDATE services SET last_billed_at = NOW() WHERE id = :id', ['id' => $serviceId]);
            continue;
        }

        db()->beginTransaction();
        try {
            $user = db_fetch_one('SELECT balance FROM users WHERE id = :id FOR UPDATE', ['id' => $userId]);
            $balance = $user ? (float)$user['balance'] : 0.0;

            if ($balance < $fee) {
                db_exec("UPDATE services SET status = 'suspended' WHERE id = :id", ['id' => $serviceId]);
                db()->commit();
                continue;
            }

            db_exec('UPDATE users SET balance = balance - :fee WHERE id = :uid', ['fee' => $fee, 'uid' => $userId]);
            db_exec(
                "INSERT INTO transactions (user_id, change_amount, type, ref_type, ref_id, `desc`)\n                 VALUES (:uid, :amt, 'consume', 'service', :sid, :d)",
                [
                    'uid' => $userId,
                    'amt' => -$fee,
                    'sid' => $serviceId,
                    'd' => 'Metered billing charge (' . $seconds . 's)',
                ]
            );

            db_exec('UPDATE services SET last_billed_at = NOW() WHERE id = :id', ['id' => $serviceId]);
            db()->commit();
            $charged++;
        } catch (Throwable $e) {
            db()->rollBack();
            throw $e;
        }
    }

    return $charged;
}

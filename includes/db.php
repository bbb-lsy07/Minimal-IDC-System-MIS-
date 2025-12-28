<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = (string)mis_config('db.dsn');
    $user = (string)mis_config('db.user');
    $pass = (string)mis_config('db.pass');

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // If using default root credentials and connection fails, try with common alternatives
        if ($user === 'root' && $pass === '') {
            // Try common default passwords for root
            $alternativePasswords = ['', 'root', 'password', 'MisTemp123!'];
            foreach ($alternativePasswords as $altPass) {
                try {
                    $pdo = new PDO($dsn, 'root', $altPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    break; // Success, break out of the loop
                } catch (PDOException $e2) {
                    // Continue to next password attempt
                    continue;
                }
            }
        }
        
        // If still not connected, re-throw the original exception
        if (!$pdo instanceof PDO) {
            throw new PDOException(
                "Database connection failed. Please check your database configuration in config.php or config.local.php. " .
                "Error: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }

    return $pdo;
}

function db_fetch_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

function db_fetch_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
}

function db_last_insert_id(): string
{
    return db()->lastInsertId();
}

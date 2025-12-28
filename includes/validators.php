<?php

declare(strict_types=1);

function validate_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password(string $password): bool
{
    return strlen($password) >= 8;
}

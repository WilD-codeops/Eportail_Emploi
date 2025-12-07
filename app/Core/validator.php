<?php

namespace App\Core;

class Validator
{
    public static function sanitize(string $value): string
    {
        return trim($value);
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^(0\d{9}|\+33\d{9})$/', $phone);
    }

    public static function validatePostalCode(string $cp): bool
    {
        return preg_match('/^\d{5}$/', $cp);
    }

    public static function validateCity(string $city): bool
    {
        return preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/', $city);
    }

    public static function validateSiret(string $siret): bool
    {
        return preg_match('/^\d{14}$/', $siret);
    }

    public static function validatePassword(string $pass, string $confirm): bool
    {
        return strlen($pass) >= 6 && $pass === $confirm;
    }
}
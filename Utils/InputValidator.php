<?php

class InputValidator
{
    public static function sanitizeText($value, int $maxLength = 255): string
    {
        $value = trim((string)($value ?? ''));
        $value = strip_tags($value);
        $value = preg_replace('/[\r\n\t]+/u', ' ', $value);
        $value = preg_replace('/\s{2,}/u', ' ', $value);
        if ($maxLength > 0) {
            $value = mb_substr($value, 0, $maxLength);
        }
        return $value;
    }

    public static function sanitizeAlphaNum($value, int $maxLength = 255): string
    {
        $value = self::sanitizeText($value, $maxLength);
        $value = preg_replace('/[^A-Za-z0-9]/u', '', $value);
        return $value;
    }

    public static function sanitizeDigits($value, int $maxLength = 30): string
    {
        $value = preg_replace('/\D+/u', '', (string)($value ?? ''));
        if ($maxLength > 0) {
            $value = substr($value, 0, $maxLength);
        }
        return $value;
    }

    public static function sanitizeEmail($value): string
    {
        $value = trim((string)($value ?? ''));
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        return $value ?: '';
    }

    public static function sanitizePassword($value, int $maxLength = 255): string
    {
        $value = trim((string)($value ?? ''));
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', '', $value);
        if ($maxLength > 0) {
            $value = substr($value, 0, $maxLength);
        }
        return $value;
    }

    public static function sanitizeFloatString($value, int $maxLength = 30): string
    {
        $value = str_replace(',', '.', trim((string)($value ?? '')));
        if (!is_numeric($value)) {
            return '';
        }
        if ($maxLength > 0) {
            $value = substr($value, 0, $maxLength);
        }
        return $value;
    }

    public static function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isInArray(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    public static function ensureNullableText($value, int $maxLength = 255): string
    {
        $value = self::sanitizeText($value, $maxLength);
        return $value;
    }
}

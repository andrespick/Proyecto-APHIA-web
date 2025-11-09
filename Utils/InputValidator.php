<?php

class InputValidator
{
    private const DEFAULT_MAX = 255;

    private static function normalize(?string $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $value = trim($value);
        return preg_replace('/\s+/u', ' ', $value) ?? '';
    }

    private static function truncate(string $value, int $maxLength): string
    {
        $maxLength = $maxLength > 0 ? $maxLength : self::DEFAULT_MAX;
        return strlen($value) > $maxLength ? substr($value, 0, $maxLength) : $value;
    }

    public static function sanitizeText(?string $value, int $maxLength): string
    {
        $value = strip_tags(self::normalize($value));
        return self::truncate($value, $maxLength);
    }

    public static function sanitizeAlphaNum(?string $value, int $maxLength): string
    {
        $value = preg_replace('/[^A-Za-z0-9]/', '', self::normalize($value)) ?? '';
        return self::truncate($value, $maxLength);
    }

    public static function sanitizeDigits(?string $value, int $maxLength): string
    {
        $value = preg_replace('/[^0-9]/', '', self::normalize($value)) ?? '';
        return self::truncate($value, $maxLength);
    }

    public static function sanitizeEmail(?string $value): string
    {
        $value = strtolower(self::normalize($value));
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        return $value === false ? '' : $value;
    }

    public static function sanitizePassword(?string $value, int $maxLength = self::DEFAULT_MAX): string
    {
        $value = is_string($value) ? trim($value) : '';
        return self::truncate($value, $maxLength);
    }

    public static function sanitizeFloatString(?string $value): string
    {
        $value = self::normalize($value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9\.\-]/', '', $value) ?? '';

        $parts = explode('.', $value);
        if (count($parts) > 2) {
            $value = $parts[0] . '.' . implode('', array_slice($parts, 1));
        }

        return $value;
    }

    public static function ensureNullableText(?string $value, int $maxLength): string
    {
        return self::sanitizeText($value, $maxLength);
    }

    public static function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isInArray(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }
}

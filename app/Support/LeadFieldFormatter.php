<?php

namespace App\Support;

class LeadFieldFormatter
{
    /**
     * @return array<int, array{key: string, label: string, value: string, type: string}>
     */
    public static function extraDataItems(mixed $data): array
    {
        return collect(self::flatten($data))
            ->map(fn (mixed $value, string $key): array => [
                'key' => $key,
                'label' => self::humanizeKey($key),
                'value' => self::valueToText($value),
                'type' => self::detectType($value),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function flatten(mixed $data, string $prefix = ''): array
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = json_last_error() === JSON_ERROR_NONE ? $decoded : ['value' => $data];
        }

        if (! is_array($data)) {
            return blank($data) ? [] : ['value' => $data];
        }

        $flattened = [];

        foreach ($data as $key => $value) {
            $key = trim((string) $key);

            if ($key === '') {
                continue;
            }

            $name = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value) && self::isAssociative($value)) {
                $flattened = [
                    ...$flattened,
                    ...self::flatten($value, $name),
                ];

                continue;
            }

            $flattened[$name] = $value;
        }

        return $flattened;
    }

    public static function humanizeKey(string $key): string
    {
        $translationKey = "admin.extra_fields.{$key}";
        $translation = __($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        $label = str_replace(['_', '-', '.'], ' ', $key);
        $label = trim(preg_replace('/\s+/', ' ', $label) ?? '');

        return $label !== '' ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8') : 'حقل غير معروف';
    }

    public static function valueToText(mixed $value): string
    {
        if (is_array($value)) {
            if ($value === []) {
                return '-';
            }

            $items = [];

            foreach ($value as $itemKey => $itemValue) {
                $text = self::valueToText($itemValue);

                if (is_string($itemKey)) {
                    $items[] = self::humanizeKey($itemKey) . ': ' . $text;
                } else {
                    $items[] = $text;
                }
            }

            return implode('، ', array_filter($items, fn (string $item): bool => $item !== '-'));
        }

        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        return trim((string) $value);
    }

    public static function detectType(mixed $value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        $text = self::valueToText($value);

        if ($text === '-') {
            return 'empty';
        }

        if (filter_var($text, FILTER_VALIDATE_URL)) {
            return 'url';
        }

        if (filter_var($text, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (mb_strlen($text) > 120) {
            return 'long';
        }

        return 'text';
    }

    /**
     * @param  array<mixed>  $value
     */
    private static function isAssociative(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }
}

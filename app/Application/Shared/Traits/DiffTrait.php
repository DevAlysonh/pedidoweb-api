<?php

namespace App\Application\Shared\Traits;

trait DiffTrait
{
    private function diff(array $old, array $new, string $prefix = ''): array
    {
        $changes = [];

        foreach ($new as $key => $value) {
            $fullKey = $prefix ? "$prefix.$key" : $key;

            if (!array_key_exists($key, $old)) {
                $changes[$fullKey] = [
                    'old' => null,
                    'new' => $value,
                ];
            } elseif (is_array($value) && is_array($old[$key])) {
                $nestedChanges = $this->diff($old[$key], $value, $fullKey);
                $changes = array_merge($changes, $nestedChanges);
            } elseif ($old[$key] !== $value) {
                $changes[$fullKey] = [
                    'old' => $old[$key],
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }
}

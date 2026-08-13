<?php

namespace App\Services;

class EnvironmentFileUpdater
{
    /** @param array<string, bool|int|string|null> $values */
    public function update(array $values, ?string $path = null): void
    {
        $environmentPath = $path ?? app()->environmentFilePath();
        $contents = file_exists($environmentPath) ? (string) file_get_contents($environmentPath) : '';

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->encode($value);
            $pattern = '/^'.preg_quote($key, '/').'\s*=.*$/m';

            $contents = preg_match($pattern, $contents) === 1
                ? (string) preg_replace($pattern, $line, $contents)
                : rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($environmentPath, $contents, LOCK_EX);
    }

    private function encode(bool|int|string|null $value): string
    {
        if ($value === null || $value === '') {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        return '"'.addcslashes($value, "\\\"").'"';
    }
}

<?php

it('keeps Bengali copy out of Blade views', function (): void {
    $bladeFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__.'/../../resources/views', FilesystemIterator::SKIP_DOTS)
    );

    $filesWithBengaliText = [];

    foreach ($bladeFiles as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $contents = file_get_contents($file->getPathname());

        if (preg_match('/[\x{0980}-\x{09FF}]/u', $contents) === 1) {
            $filesWithBengaliText[] = str_replace(realpath(__DIR__.'/../..').DIRECTORY_SEPARATOR, '', $file->getPathname());
        }
    }

    expect($filesWithBengaliText)->toBeEmpty();
});

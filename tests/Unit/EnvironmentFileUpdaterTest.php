<?php

use App\Services\EnvironmentFileUpdater;

it('updates existing environment values and appends missing values', function () {
    $path = tempnam(sys_get_temp_dir(), 'nutms-env-');
    file_put_contents($path, "MAIL_HOST=localhost\nAPP_NAME=Nutms\n");

    (new EnvironmentFileUpdater)->update([
        'MAIL_HOST' => 'smtp.example.com',
        'MAIL_PORT' => 587,
        'MAIL_PASSWORD' => 'secret with spaces',
    ], $path);

    $contents = file_get_contents($path);
    unlink($path);

    expect($contents)
        ->toContain('MAIL_HOST="smtp.example.com"')
        ->toContain('MAIL_PORT=587')
        ->toContain('MAIL_PASSWORD="secret with spaces"')
        ->toContain('APP_NAME=Nutms');
});

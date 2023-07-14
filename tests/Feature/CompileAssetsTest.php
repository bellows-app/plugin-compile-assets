<?php

use Bellows\Plugins\CompileAssets;
use Bellows\PluginSdk\Facades\Npm;

it('is disabled when is no build script', function () {
    expect($this->plugin(CompileAssets::class)->defaultForDeployConfirmation())->toBeFalse();
});

it('is enabled with a build script and a lock file', function () {
    Npm::addScriptCommand('build', 'echo "building"');
    expect($this->plugin(CompileAssets::class)->defaultForDeployConfirmation())->toBeTrue();
});

it('adds the correct commands to the deploy script', function ($packageManager, $expected) {
    $this->setJsPackageManager($packageManager);
    Npm::addScriptCommand('build', 'echo "building"');
    $result = $this->plugin(CompileAssets::class)->deploy();

    expect($result->getUpdateDeployScript())->toContain($expected);
})->with([
    ['yarn', 'yarn' . PHP_EOL . 'yarn build'],
    ['npm', 'npm install' . PHP_EOL . 'npm run build'],
]);

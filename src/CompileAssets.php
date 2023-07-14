<?php

namespace Bellows\Plugins;

use Bellows\PluginSdk\Contracts\Deployable;
use Bellows\PluginSdk\Facades\Deployment;
use Bellows\PluginSdk\Facades\DeployScript;
use Bellows\PluginSdk\Facades\Npm;
use Bellows\PluginSdk\Plugin;
use Bellows\PluginSdk\PluginResults\CanBeDeployed;
use Bellows\PluginSdk\PluginResults\DeploymentResult;

class CompileAssets extends Plugin implements Deployable
{
    use CanBeDeployed;

    protected array $yarnLines = [
        'yarn',
        'yarn build',
    ];

    protected array $npmLines = [
        'npm install',
        'npm run build',
    ];

    public function defaultForDeployConfirmation(): bool
    {
        return Npm::getPackageManager() !== null && Npm::hasScriptCommand('build');
    }

    public function deploy(): ?DeploymentResult
    {
        return DeploymentResult::create()->updateDeployScript(
            fn () => DeployScript::addAfterComposerInstall(
                match (Npm::getPackageManager()) {
                    'yarn'  => $this->yarnLines,
                    default => $this->npmLines,
                },
            ),
        );
    }

    public function shouldDeploy(): bool
    {
        return !Deployment::site()->isInDeploymentScript($this->yarnLines)
            && !Deployment::site()->isInDeploymentScript($this->npmLines);
    }
}

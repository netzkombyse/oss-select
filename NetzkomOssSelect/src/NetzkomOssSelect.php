<?php declare(strict_types=1);


namespace Netzkom\OssSelect;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NetzkomOssSelect extends Plugin
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        // call parent
        parent::build($container);
    }

    /**
     * {@inheritDoc}
     */
    public function activate(ActivateContext $activateContext): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstallContext $installContext): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function postInstall(InstallContext $installContext): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function update(UpdateContext $updateContext): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(UpdateContext $updateContext): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(UninstallContext $context): void
    {
    }
}

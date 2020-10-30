<?php

namespace Divante\AutoImportBundle\EventListener;

use Divante\AutoImportBundle\AutoImportBundle;
use Divante\AutoImportBundle\Service\ImportDefintions;
use Divante\AutoImportBundle\Service\Settings;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Logger;
use Pimcore\Model\Asset;

/**
 * Class AssetEventListener
 * @package Divante\AutoImportBundle\EventListener
 */
class AssetEventListener
{
    /**
     * Import Definitions Service
     *
     * @var ImportDefintions
     */
    private $definitionsService;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * AssetEventListener constructor.
     *
     * @param ImportDefintions $definitionsService Import Definitions Service
     */
    public function __construct(ImportDefintions $definitionsService)
    {
        $this->definitionsService = $definitionsService;
        $this->settings = new Settings();
    }

    /**
     * @param AssetEvent $event
     *
     * @return void
     */
    public function onPostAdd(AssetEvent $event): void
    {
        $asset = $event->getAsset();
        if (!$integration = $this->isAssetValid($asset)) {
            return;
        }

        $this->processFile($asset, $integration);
    }

    /**
     * Processes asset file.
     *
     * @param Asset $asset
     * @param Settings\Integration $integration
     * @return void
     *
     * @throws \Exception
     */
    private function processFile(Asset $asset, Settings\Integration $integration): void
    {
        $asset->setParent(
            Asset\Service::createFolderByPath(
                $integration->getFolder() . '/' . AutoImportBundle::PROCESSING_DIRECTORY
            )
        );

        try {
            $existingAsset = Asset::getByPath($asset->getFullPath());
            if ($existingAsset) {
                $asset->setFilename(Asset\Service::getUniqueKey($asset));
            }
            $asset->save();
            try {
                $this->runImport($asset, $integration);
            } catch (\Exception $exception) {
                ApplicationLogger::getInstance()->alert(
                    AutoImport::class . ": import for file " . $asset->getFullPath()
                    . " couldn't be run. Reason: " . $exception->getMessage()
                );
                Logger::err($exception->getMessage());
                Logger::err(($exception->getTraceAsString()));
                $asset->setParent(
                    Asset\Service::createFolderByPath(
                        $integration->getFolder() . '/' . AutoImportBundle::ERROR_DIRECTORY
                    )
                );
                try {
                    $asset->save();
                } catch (\Exception $exception) {
                    $asset->setFilename(Asset\Service::getUniqueKey($asset));
                    $asset->save();
                    ApplicationLogger::getInstance()->alert(
                        AutoImport::class . ": file " . $asset->getFullPath()
                        . " has been saved under different name."
                    );
                }
            }
        } catch (\Exception $exception) {
            ApplicationLogger::getInstance()->alert(
                AutoImport::class . ": file " . $asset->getFullPath()
                . " couldn't been saved."
            );
        }
    }

    /**
     * Runs import on the Asset
     *
     * @param Asset $asset Asset to be imported
     *
     * @param Settings\Integration $integration
     * @return void
     * @throws \Exception
     */
    protected function runImport(Asset $asset, Settings\Integration $integration): void
    {
        $this->definitionsService->runImportByName(
            $integration->getImportDefinition(),
            $asset
        );
    }

    /**
     * Checks if asset is valid for Import.
     *
     * @param Asset $asset Asset for validation
     *
     * @return bool
     */
    protected function isAssetValid(Asset $asset)
    {
        if ($asset->getType() != "text") {
            return false;
        }

        foreach ($this->settings->getIntegrations() as $integration) {
            if ($asset->getParent()->getPath() == $integration->getFolder()->getPath()) {
                return $integration;
            }
        }

        return false;
    }
}

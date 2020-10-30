<?php

namespace Divante\AutoImportBundle\EventListener;

use Divante\AutoImportBundle\AutoImport;
use Divante\AutoImportBundle\AutoImportBundle;
use Divante\AutoImportBundle\Service\Settings;
use Pimcore\Model\Asset;
use Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent;

/**
 * Class ImportDefinitionsEventListener
 * @package Divante\AutoImportBundle\EventListener
 */
class ImportDefinitionsEventListener
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * ImportDefinitionsEventListener constructor.
     */
    public function __construct()
    {
        $this->settings = new Settings();
    }

    /**
     * @param ImportDefinitionEvent $event
     *
     * @return void
     */
    public function onProcessingFailure(\Wvision\Bundle\DataDefinitionsBundle\Event\ImportDefinitionEvent $event): void
    {
        $this->moveAsset($event, AutoImportBundle::ERROR_DIRECTORY);
    }

    /**
     * @param ImportDefinitionEvent $event
     *
     * @return void
     */
    public function onProcessingSuccess(ImportDefinitionEvent $event): void
    {
        $this->moveAsset($event, AutoImportBundle::DONE_DIRECTORY);
    }

    /**
     * @param ImportDefinitionEvent $event
     * @param string $folder
     * @return void|null
     */
    private function moveAsset(ImportDefinitionEvent $event, string $folder)
    {
        $definition = $event->getDefinition();

        $currentIntegration = null;
        /** @var Settings\Integration $integration */
        foreach ($this->settings->getIntegrations() as $integration) {
            if ($integration->getImportDefinition() == $definition->getName()) {
                $currentIntegration = $integration;
                break;
            }
        }

        if (!$currentIntegration) {
            return;
        }

        $assetId = $event->getSubject()['assetId'];
        $asset = Asset::getById($assetId);
        if (!$asset) {
            return;
        }

        if (str_starts_with($asset->getPath(), $currentIntegration->getFolder())) {
            try {
                $asset->setParent(
                    Asset\Service::createFolderByPath(
                        $currentIntegration->getFolder() . '/' . $folder
                    )
                );
                $existingAsset = Asset::getByPath($asset->getFullPath());
                if ($existingAsset) {
                    $asset->setFilename(Asset\Service::getUniqueKey($asset));
                }
                $asset->save();
                return;
            } catch (\Exception $e) {
                return;
            }
        }
    }
}

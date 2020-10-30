<?php

namespace Divante\AutoImportBundle\Service;

use Divante\AutoImportBundle\AutoImportBundle;
use Divante\AutoImportBundle\Service\Settings\Integration;
use Pimcore\Model\WebsiteSetting;
use Pimcore\Model\WebsiteSetting\Listing;

/**
 * Class Settings
 * @package Divante\AutoImportBundle\Service
 */
class Settings
{
    public const INTEGRATIONS_PREFIX = AutoImportBundle::CLASS_PREFIX . '_INTEGRATION';
    public const CONDITION_PREFIX = AutoImportBundle::CLASS_PREFIX . '_CONDITION';
    public const DEFINITION_PREFIX = AutoImportBundle::CLASS_PREFIX . '_DEFINITION';
    public const FOLDER_PREFIX = AutoImportBundle::CLASS_PREFIX . '_FOLDER';

    private $integrations = [];

    /**
     * @return array
     */
    public function getIntegrations(): array
    {
        return $this->integrations;
    }

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $websiteSettings = $this->getBundleWebsiteSettings();
        foreach ($websiteSettings as $websiteSetting) {
            $integration = $this->createIntegrationOrNull($websiteSetting);
            if ($integration) {
                $this->integrations[] = $integration;
            }
        }
    }

    /**
     * @return WebsiteSetting[]
     */
    public function getBundleWebsiteSettings(): array
    {
        $websiteSettingsListing = new Listing();
        $websiteSettings = $websiteSettingsListing->getSettings();
        $bundleWebsiteSettings = [];

        /** @var WebsiteSetting $websiteSetting */
        foreach ($websiteSettings as $websiteSetting) {
            if ($this->checkIfBundleWebsiteSetting($websiteSetting->getName())) {
                $bundleWebsiteSettings[] = $websiteSetting;
            }
        }

        return $bundleWebsiteSettings;
    }

    /**
     * @param string $wsName
     * @return bool
     */
    protected function checkIfBundleWebsiteSetting(string $wsName): bool
    {
        return str_starts_with($wsName, self::INTEGRATIONS_PREFIX);
    }

    /**
     * @param WebsiteSetting $websiteSetting
     * @return Integration|null
     */
    protected function createIntegrationOrNull(WebsiteSetting $websiteSetting): ?Integration
    {
        $integrationName = $websiteSetting->getData();
        if (!$integrationName) {
            return null;
        }

        $suffix = $this->getWebsiteSettingSuffix($websiteSetting->getName(), $integrationName);
        $definition = WebsiteSetting::getByName(self::DEFINITION_PREFIX . $suffix);
        $folder = WebsiteSetting::getByName(self::FOLDER_PREFIX . $suffix);
        if ($definition && $definition->getData() && $folder && $folder->getData()) {
            $integration = new Integration();
            $integration->setName($integrationName);
            $integration->setImportDefinition($definition->getData());
            $integration->setFolder($folder->getData());

            return $integration;
        }
        return null;
    }

    /**
     * @param string $wsName
     * @param string $integrationName
     * @return string
     */
    protected function getWebsiteSettingSuffix(string $wsName, string $integrationName): string
    {
        return $wsName == self::INTEGRATIONS_PREFIX ? '' : '_' . $integrationName;
    }
}

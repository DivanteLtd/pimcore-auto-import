<?php

namespace Divante\AutoImportBundle;

use Divante\AutoImportBundle\Service\Settings;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\WebsiteSetting;

/**
 * Class Installer
 * @package Divante\AutoImportBundle
 */
class Installer extends AbstractInstaller
{
    const SAMPLE_SETTINGS = [
        [
            'type' => 'asset',
            'name' => Settings::FOLDER_PREFIX
        ],
        [
            'type' => 'text',
            'name' => Settings::INTEGRATIONS_PREFIX
        ],
        [
            'type' => 'text',
            'name' => Settings::DEFINITION_PREFIX
        ]
    ];

    /**
     * @return bool
     */
    public function install()
    {
        foreach (self::SAMPLE_SETTINGS as $setting) {
            $websiteSetting = new WebsiteSetting();
            $websiteSetting->setName($setting['name']);
            $websiteSetting->setType($setting['type']);
            $websiteSetting->save();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        $settings = new Settings();
        if (count($settings->getIntegrations()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canBeUninstalled()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }
}

<?php

namespace Divante\AutoImportBundle\Service\Settings;

use Pimcore\Model\Asset\Folder;

/**
 * Class Integration
 * @package Divante\AutoImportBundle\Service\Settings
 */
class Integration
{
    public $name;
    public $importDefinition;
    public $condition;

    /** @var Folder */
    public $folder;

    /**
     * @return Folder
     */
    public function getFolder(): Folder
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     */
    public function setFolder(Folder $folder): void
    {
        $this->folder = $folder;
    }

    /**
     * @return string
     */
    public function getImportDefinition(): string
    {
        return $this->importDefinition;
    }

    /**
     * @param string $importDefinition
     */
    public function setImportDefinition(string $importDefinition): void
    {
        $this->importDefinition = $importDefinition;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

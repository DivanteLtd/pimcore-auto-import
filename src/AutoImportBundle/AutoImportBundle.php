<?php

namespace Divante\AutoImportBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * Class AutoImportBundle
 * @package Divante\AutoImportBundle
 */
class AutoImportBundle extends AbstractPimcoreBundle
{
    public const CLASS_PREFIX = "AutoImport";
    public const ROOT_DIRECTORY = "/DAX/";
    public const INGOING_DIRECTORY = self::ROOT_DIRECTORY . "ingoing/";
    public const OUTGOING_DIRECTORY = self::ROOT_DIRECTORY . "outgoing/";
    public const INGOING_DIRECTORY_PRODUCT = self::INGOING_DIRECTORY . "product/";
    public const OUTGOING_DIRECTORY_PRODUCT = self::OUTGOING_DIRECTORY . "product/";
    public const OUTGOING_DIRECTORY_REQUEST = self::OUTGOING_DIRECTORY . "request/";

    public const PROCESSING_DIRECTORY = "processing";
    public const DONE_DIRECTORY = "done";
    public const ERROR_DIRECTORY = "error";

    /**
     * @return array|\Pimcore\Routing\RouteReferenceInterface[]|string[]
     */
    public function getJsPaths()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getNiceName()
    {
        return "AutoImport";
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Automatically runs Import Definitions on created assets";
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return new Installer();
    }
}

<?php

namespace Divante\AutoImportBundle\Service;

use Divante\ImporterBundle\Exception\DefinitionNotFoundException;
use Divante\ImporterBundle\Exception\ExecutableNotFoundException;
use Pimcore\Db;
use Pimcore\Model\Asset;
use ProcessManagerBundle\Model\Executable;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Wvision\Bundle\DataDefinitionsBundle\Model\ImportDefinition;
use Wvision\Bundle\DataDefinitionsBundle\Repository\DefinitionRepository;

/**
 * Class ImportDefintions
 * @package Divante\AutoImportBundle\Service
 */
class ImportDefintions
{
    use ContainerAwareTrait;

    /**
     * @param string $importName
     * @param Asset $fileToImport
     * @throws \Exception
     */
    public function runImportByName(string $importName, Asset $fileToImport)
    {
        /** @var ImportDefinition $definition */
        $definition = $this->container->get('data_definitions.repository.import_definition')->findByName($importName);

        if (is_null($definition)) {
            throw new DefinitionNotFoundException(
                sprintf(
                    "Unable to find import definition with name = %s",
                    $importName
                )
            );
        }

        $executableIds = $this->getExecutableIds($importName);
        if (empty($executableIds)) {
            $executable = new Executable();
            $executable->setName($importName);
            $executable->setType('importdefinition');
            $executable->setDescription('Executable for import ' . $importName . ' - generated by AutoImport.');
            $executable->setCron('');
            $executable->setActive(0);
        } else {
            $executable = Executable::getById($executableIds[0]['id']);
            if (!$executable) {
                throw new ExecutableNotFoundException(
                    sprintf(
                        "Unable to find Executable with name = %s",
                        $importName
                    )
                );
            }
        }

        $params = [
            "file" => $fileToImport->getFileSystemPath(),
            "assetId" => $fileToImport->getId(),
        ];

        $executable->setSettings(
            [
                'definition' => $definition->getId(),
                'params' => json_encode($params),
                'foreground' => true,
            ]
        );

        $executable->save();

        $this->container->get('process_manager.registry.processes')
            ->get($executable->getType())
            ->run($executable);
    }

    /**
     * @param string $name
     * @return mixed[]
     */
    public function getExecutableIds(string $name)
    {
        $connection = Db::getConnection();
        $query = $connection
            ->select("id")
            ->from("process_manager_executables")
            ->where("name = ?", $name);

        return $query->execute()->fetchAll();
    }
}

# AutoImport for Pimcore

This bundle allows running DataDefinitions (https://github.com/w-vision/DataDefinitions/) imports automatically for created assets.

## Installing

```bash
composer require divanteltd/pimcore-auto-import
```

Enable and install the Bundle in admin panel or in command line:
```bash
./bin/console pimcore:bundle:enable AutoImportBundle
./bin/console pimcore:bundle:install AutoImportBundle
```

## EventListeners
Bundle listens on event:

* `pimcore.asset.postAdd` - handled by \Divante\AutoImport\EventListener\AssetEventListener::onPostAdd
* `import_definition.*` - handled by \Divante\AutoImport\EventListener\ImportDefinitionsEventListener

## Requirements

* ProcessManager (https://github.com/dpfaffenbauer/ProcessManager)
* DataDefinitions (https://github.com/w-vision/DataDefinitions/)

## Usage

After installation open up Website settings and fill in:
- `AutoImport_INTEGRATION` - integration name
- `AutoImport_DEFINITION` - name of the Import Defintion
- `AutoImport_FOLDER` - folder which bundle will monitor for incoming files

![Website settings example](docs/img/website-settings.png?raw=true)

From now on, all assets added to the configured folder (`product` in example above) will be used as an input to defined Import Definition.
Executable in Process Manager will be createad automatically, alongside with additional folder structure,
as subfolders of the one defined in Website Settings (if needed):
- `/processing` - files being currently imported
- `/done` - files sucessfully imported
- `/error` - files which import ended with an error

![Folder structure](docs/img/folder-structure.png?raw=true)

## Tips

### Mutliple integrations

You can define additional watch folders and definitions, by adding additional Website Settings:
- `AutoImport_INTEGRATION_sampleName` - last part is used to find other configuration lines
- `AutoImport_DEFINITION_sampleName`
- `AutoImport_FOLDER_sampleName`

### Automatize asset creation based on filesystem changes

If external system is uploading the file to the filesystem directly (not using admin panel, GraphQL or WebDAV),
you can easily synchronize the filesystem changes using Pimcore Devkit (https://github.com/DivanteLtd/pimcore-devkit):

```bash
./bin/console devkit:asset:synchronize /ERP/ingoing
```

Using Process Manager's build-in cron functionality, you can easily run this command periodically.

### Two-way communication

If your integration is entirely based on file exchange, you can use this bundle with
Scheduled Export (https://github.com/DivanteLtd/pimcore-scheduled-export) to configure complete integration in Pimcore
admin panel.

## Contributing
If you'd like to contribute, please fork the repository and use a feature branch. Pull requests are warmly welcome.

## Licence 
Pimcore Scheduled Export source code is completely free and released under the 
[GNU General Public License v3.0](https://github.com/DivanteLtd/divante-ltd/pimcore-scheduled-export/blob/master/LICENSE).

## Standards & Code Quality
This module respects all Pimcore code quality rules and our own PHPCS and PHPMD rulesets.

## About Authors
![Divante-logo](http://divante.co/logo-HG.png "Divante")

We are a Software House from Europe, existing from 2008 and employing about 150 people. Our core competencies are built 
around Magento, Pimcore and bespoke software projects (we love Symfony3, Node.js, Angular, React, Vue.js). 
We specialize in sophisticated integration projects trying to connect hardcore IT with good product design and UX.

We work for Clients like INTERSPORT, ING, Odlo, Onderdelenwinkel and CDP, the company that produced The Witcher game. 
We develop two projects: [Open Loyalty](http://www.openloyalty.io/ "Open Loyalty") - an open source loyalty program 
and [Vue.js Storefront](https://github.com/DivanteLtd/vue-storefront "Vue.js Storefront").

We are part of the OEX Group which is listed on the Warsaw Stock Exchange. Our annual revenue has been growing at a 
minimum of about 30% year on year.

Visit our website [Divante.com](https://divante.com/ "Divante.co") for more information.

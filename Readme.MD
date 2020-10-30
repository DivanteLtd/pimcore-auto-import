# AutoImport for Pimcore

## Install

## EventListeners
Bundle listens on event:

* `pimcore.asset.postAdd` - handled by \Divante\AutoImport\EventListener\AssetEventListener::onPostAdd
* `import_definition.*` - handled by \Divante\AutoImport\EventListener\ImportDefinitionsEventListener

Enable Translator :

# app/config/config.yml
framework:
    translator: { fallbacks: [en] }

routing_dev.yml::

    translator_tool:
        resource: "@TranslatorToolBundle/Controller/"
        type:     annotation
        prefix:   /translator_tool/


config_dev.yml::

    translator_tool:
        auto_create_missing:
            enabled: true
            format: yml

AppKernel.php::

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        ...
        $bundles[] = new AECF\TranslatorToolBundle\TranslatorToolBundle();
    }
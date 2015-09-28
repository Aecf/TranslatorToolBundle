
Fichier routing_dev.yml::

    translator_tool:
        resource: "@TranslatorToolBundle/Controller/"
        type:     annotation
        prefix:   /translator_tool/


Fichier config_dev.yml::


    translator_tool:
        auto_create_missing:
            enabled: true
            format: yml
        
Fichier AppKernel.php::

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        ...
        $bundles[] = new MD\TranslatorToolBundle\TranslatorToolBundle();
    }
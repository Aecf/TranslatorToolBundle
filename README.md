AECF/TranslatorToolBundle
=============

The TranslatorToolBundle adds a symfony profiler tool.

Features include:

- Automatically adds your keys followed by "trans" twig filter in the translation file (create if it does not exist).
- Adds an interface to add or edit translations, just click on the contents of your word in the "Message Preview" column, type your word and press enter.
- Only the language of your current environment will be impacted.
- Unit tested

**Note:** This bundle does *not* provide an translation system but make it easier

[![Build Status](https://travis-ci.org/Aecf/TranslatorToolBundle.svg?branch=master)](https://travis-ci.org/Aecf/TranslatorToolBundle)

Installation
------------

You can install Slugify through [Composer](https://getcomposer.org):

```shell
$ composer require aecf/translator-tool
```

#### Enable translation :
In your app/config/config.yml, just uncomment :

    framework: translator: { fallbacks: [en] }

#### Enable the bundle :

app/AppKernel.php :

```php
if (in_array($this->getEnvironment(), array('dev', 'test'))) {
    ...
    $bundles[] = new MD\TranslatorToolBundle\TranslatorToolBundle();
}
```

routing_dev.yml :

    translator_tool:
        resource: "@TranslatorToolBundle/Controller/"
        type:     annotation
        prefix:   /translator_tool/

config_dev.yml :

    translator_tool:
        auto_create_missing:
            enabled: true
            format: yml

Result
------

![TranslatorTool Profiler](https://github.com/Aecf/TranslatorToolBundle/tree/master/Resources/doc/profiler.png)

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

About
-----

TranslatorToolBundle is a [AECF](https://github.com/Aecf) initiative.

AECF = Auchan E-Commerce France.

See also the list of [contributors](https://github.com/Aecf/TranslatorToolBundle/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Aecf/TranslatorToolBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.

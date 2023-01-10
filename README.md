# WPML Installer for composer
This is an composer plugin to make installing [wpml.org](https://wpml.org) plugins with composer easy.
Your subscription info will be read from environment variables, will only be transmitted to WPML and will not be displayed in `composer.lock`.

This composer plugin does not provide any WPML code. The WPML plugins will be downloaded directly from wpml.org

## Usage

#### 1. Add our [composer repository for WPML packages](https://github.com/lucguerraz/wpml-packages) to the `repositories` section of your `composer.json` file or define the packages ad-hoc
```
{
  "type": "composer",
  "url": "https://lucguerraz.github.io/wpml-packages/"
}
```
This installs the package as `wordpress-plugin` type and requires [`composer/installers`](https://packagist.org/packages/composer/installers), so you can install the plugins in the correct location.

This repository provides the latest versions of the WPML plugins. If you want a specific version you can define the packages ad-hoc and just require `lucguerraz/wpml-installer`. A little bit like this:

```
{
  "type": "package",
  "package": {
    "name": "wpml/sitepress-multilingual-cms",
    "version": "1.0.0",
    "type": "wordpress-plugin",
    "dist": {
        "type": "zip",
        "url": "https://wpml.org/?download=6088&version=1.0.0"
    },
    "require": {
        "lucguerraz/wpml-installer": "^0.1",
        "composer/installers": "~1.0"
    }
  }
}
```
When you request an older version you must be sure than WPML still provides a download otherwise this won't work

### 2. Save your WPML user id and subscription key to environment variables

We do not provide any WPML code, the plugins are downloaded directly from WPML servers. Because of this we need your WPML subscription info to be able to download them. Your subscription info is stored in environment variables, is only transmitted to WPML and will not show up in `composer.lock`.

You can get your user id and subscription key from a WPML provided download link, that you can get from your [WPML dashboard](https://wpml.org/account/downloads/). It will look like this:
`https://wpml.org/?download=6088&user_id=XXXXX&subscription_key=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX&version=4.5.14`

You must save the `user_id` parameter in the `WPML_USER_ID` environment variable and the `subscription_key` parameter in the `WPML_SUBSCRIPTION_KEY` environment variable.

### 3. Require a WPML plugin

`composer require wpml/sitepress-multilingual-cms`

## Acknowledgments

This composer plugin was created to add support to installing WPML with composer v2. It was heavily inspired by [`pernod-ricard-brandcos/wpml-installer`](https://bitbucket.org/pernod-ricard-brandcos/wpml-installer) and [`enelogic/wpml-installer`](https://github.com/enelogic/wpml-installer).

## Disclaimer

This project is not affiliated with WPML in any way, we do not provide any WPML code, all plugins are directly downloaded from wpml.org, your subscription info will be injected dynamically into the download link, your subscription info will only be transmitted to WPML and will not be displayed in `composer.lock`

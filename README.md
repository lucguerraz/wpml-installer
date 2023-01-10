# WPML Installer for composer
A composer plugin that makes installing WPML with composer easy.
It reads your WPML user id and subscription info from environment variables
This Plugin does not provide any WPML code. The plugins will be downloaded directly from wpml.org

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

We do not provide any WPML code, the plugins are downloaded directly from WPML servers. Because of this we need your WPML subscription info to be able to download them. Your subscription info stays on your device and is only transmitted to WPML

You can get your user id and subscription key from a WPML provided download link, that you can get from your WPML dashboard. It will look like this:
`https://wpml.org/?download=6088&user_id=XXXXX&subscription_key=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX&version=4.5.14`

You must save the `user_id` parameter in the `WPML_USER_ID` variable and the `subscription_key` parameter in the `WPML_SUBSCRIPTION_KEY` variable.

### 3. Require a WPML plugin

`composer require wpml/sitepress-multilingual-cms`

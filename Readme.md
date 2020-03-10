# TYPO3 Extension ``site_generator``

>With this backend extension, you can very easily create min-website or duplicate tree, it will automatically create associated BE/FE groups, create directories with associated files mount, add domain name, update Typoscript configuration (folders/pages ID and TCEMAIN.clearCacheCmd), update slugs. Based on State Design Pattern, it is highly customizable : you can remove unnecessary states and add your own states to fit your own needs.

## Features

- Based on extbase & fluid, implementing best practices from TYPO3 CMS
- Based on State Design Pattern
- Highly customizable to fit your own needs
- Use multiple models
- You can specify many pages to call the wizard

### Installation

#### Installation using Composer

The recommended way to install the extension is by using [Composer][2]. In your Composer based TYPO3 project root, just do `composer require oktopuce/site-generator`.

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the extension with the extension manager module.

### Minimal setup

1) Install the extension
2) Create your model and a root page for site generation
2) Within extension configuration, set 'Models Pid' and 'Sites Pid'
3) Then you can call the wizard on 'Sites Pid' pages

## Reporting bugs / Contributions
Anyone is welcome to contribute to Site Generator. Please
[read the guidelines](.github/CONTRIBUTING.md) for contributing to this
repository and how to report bugs.

There are various ways you can contribute:

* [Raise an issue](https://github.com/Oktopuce/site_generator/issues) on GitHub.
* Send me a Pull Request with your bug fixes and/or new features.

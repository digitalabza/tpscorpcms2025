# Orange Starter

A base for creating a Drupal theme.

## Contents

* [Introduction](#introduction).
* [Requirements](#requirements).
* [Installation](#installation).
* [Configuration](#configuration).
* [Maintainers](#maintainers).

## Introduction

This theme is part of the Orange Suite developed by [Acro Media Inc.](https://www.acromedia.com/).

This is not intended as a ready-made theme. Utilise this as a base to create your own specific design.

Visit the [project page](https://www.drupal.org/project/codewrx_starter) for more information on releases, documentation, and issues. To submit bug reports, feature requests, or other issues visit the [issue queue](https://www.drupal.org/project/issues/orange_starter).

## Requirements

This theme uses SASS to provide an easier time writing styles than vanilla CSS. To utilise SASS you must have installed [NPM](https://www.npmjs.com/) and then this projects dependencies.

Once installed, you can use Gulp to compile your SASS. Run the `sass` command defined in the [Gulp File](./gulpfile.js) to do this.

## Installation

Install as you would install any other [contributed Drupal module](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules) using [Composer](https://getcomposer.org) :

1. Run `composer require drupal/codewrx_starter` (assuming you have set up the Drupal packages repository).

2. In the terminal, navigate into the theme and run `npm install && npm run gulp sass` to install dependencies and compile the CSS.

    * If this is for a production site, you can also then run `npm prune --production` to remove development dependencies.

3. Navigate to `/admin/appearance` on your site and enable the theme.

4. Copy the theme to a new namespace and create your own version of it.

## Configuration

You must enable the [Twig Tweak](https://www.drupal.org/project/twig_tweak) module. Navigate to `/admin/modules` and enable this module.

If you want to create your own custom theme you can utilise this as the base theme.

## Maintainers

Current Maintainers :

* Derek Cresswell - [derekcresswell](https://www.drupal.org/u/derekcresswell)
* Shawn McCabe - [smccabe](https://www.drupal.org/u/smccabe)
* Josh Miller - [joshmiller](https://www.drupal.org/u/joshmiller)

This project is sponsored by :

* [Acro Media Inc.](https://www.acromedia.com/)
    *  A leading ecommerce service provider, giving the insights & development online retailers need to optimize their technology for scalable growth and innovation.

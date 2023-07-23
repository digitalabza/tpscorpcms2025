# CodeWrx Starter

A base for creating a Drupal theme.

## Installation

Install as you would install any other [contributed Drupal module](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules) using [Composer](https://getcomposer.org) :

1. Run `composer require drupal/codewrx_starter` (assuming you have set up the Drupal packages repository).

2. In the terminal, navigate into the theme and run `npm install && npm run gulp sass` to install dependencies and compile the CSS.

    * If this is for a production site, you can also then run `npm prune --production` to remove development dependencies.

3. Navigate to `/admin/appearance` on your site and enable the theme.

4. Copy the theme to a new namespace and create your own version of it.

### Sass
- Configured to compile using Gulp.
- Compile to CSS by running the following commands:
  - `npm install --global gulp-cli`
  - `npm install && npm run gulp sass`
- The CSS will be compiled to: `css/style.css`
- Mode to run for production environments
  - `npm prune --production`

## Starting Your New Custom Theme

This theme is meant to be copied and renamed to become your custom theme for your project. Follow the steps below to live a happy life.

- run command from inside primary theme:
  - bash scripts/create_subtheme.sh

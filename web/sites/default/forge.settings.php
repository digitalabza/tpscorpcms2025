<?php

$databases = [];
$config_directories = [];
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];


// Set a default private files directory outside of the docroot.
$settings['file_private_path'] = '../private';

// Set a default config sync directory outside of the docroot.
// This is defined inside the read-only "config" directory, deployed via Git.
$settings['config_sync_directory'] = '../config/sync';

// Don't use Symfony's APCLoader. ddev includes APCu; Composer's APCu loader has
// better performance.
// $settings['class_loader_auto_detect'] = FALSE;


// Automatic Forge settings.
if (file_exists($app_root . '/' . $site_path . '/settings.forge.php')) {
  include $app_root . '/' . $site_path . '/settings.forge.php';
}

// Local settings. These come last so that they can override anything.
// if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
//   include $app_root . '/' . $site_path . '/settings.local.php';
// }
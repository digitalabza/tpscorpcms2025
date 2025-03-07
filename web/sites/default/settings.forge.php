<?php



$databases['default']['default'] = [
  'driver' => 'mysql',
  'database' => $_ENV['MYSQL_DATABASE'],
  'username' => $_ENV['MYSQL_USER'],
  'password' => $_ENV['MYSQL_PASSWORD'],
  'host' => $_ENV['MYSQL_HOSTNAME'],
  'port' => $_ENV['MYSQL_PORT'],
  'init_commands' => [
    'isolation_level' => 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED',
  ],
];
$databases['default']['default']['namespace'] = 'Drupal\\Core\\Database\\Driver\\mysql';

$settings['hash_salt'] = 'dc75023fe71bd42038fbb864ba03b0859b8c4427709337beec9f694c1f548abd';

// Set a default private files directory outside of the docroot.
$settings['file_private_path'] = '../private';


// Set a default public files directory outside of the docroot.
$settings['file_public_path'] = 'sites/default/files';
$config['system.file']['path.temporary'] = '/tmp';


// Set a default config sync directory outside of the docroot.
// This is defined inside the read-only "config" directory, deployed via Git.
$settings['config_sync_directory'] = '../config/sync';

// This will prevent Drupal from setting read-only permissions on sites/default.
$settings['skip_permissions_hardening'] = TRUE;

// Trusted host pattern.
// $settings['trusted_host_patterns'] = array(
//     '^shopnashuaonline\.co.za$',
//     '^.+\.shopnashuaonline\.co.za$',
//   );

/**
 * Disable tracking scripts.
 *
 * It's good practice to have these set on staging environments as well.
 */
$config['google_analytics.settings']['account'] = 'UA-XXXXXXXX-XX';
$config['google_tag.settings']['container_id'] = 'GTM-XXXXXX';
$config['google_tag.container.default']['container_id'] = 'GTM-XXXXXX';
$config['hotjar.settings']['account'] = 'XXXXXX';

// Enable Redis caching.
if (!\Drupal\Core\Installer\InstallerKernel::installationAttempted() && extension_loaded('redis') && class_exists('Drupal\redis\ClientFactory')) {
  // Set Redis as the default backend for any cache bin not otherwise specified.
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['redis.connection']['interface'] = 'PhpRedis';
  $settings['redis.connection']['host'] = '127.0.0.1';
  $settings['redis.connection']['port'] = 6379;

  // Apply changes to the container configuration to better leverage Redis.
  // This includes using Redis for the lock and flood control systems, as well
  // as the cache tag checksum. Alternatively, copy the contents of that file
  // to your project-specific services.yml file, modify as appropriate, and
  // remove this line.
  $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';

  // Allow the services to work before the Redis module itself is enabled.
  $settings['container_yamls'][] = 'modules/contrib/redis/redis.services.yml';

  // Manually add the classloader path, this is required for the container cache bin definition below
  // and allows to use it without the redis module being enabled.
  $class_loader->addPsr4('Drupal\\redis\\', 'modules/contrib/redis/src');

  // Enable Redis for specific cache bins (optional, but recommended for Commerce).
  $settings['cache']['bins']['render'] = 'cache.backend.redis';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.redis';
  $settings['cache']['bins']['page'] = 'cache.backend.redis';

  // Cache settings.
  $settings['cache_minimum_lifetime'] = 0;    // No minimum lifetime.
  // $settings['page_cache_maximum_age'] = 900;  // 15 minutes for cached pages.
  $settings['page_cache_maximum_age'] = 3600;  // 60 minutes for cached pages.

  // Enable Internal Page Cache.
  $settings['omit_vary_cookie'] = FALSE;

  // Use redis for container cache.
  // The container cache is used to load the container definition itself, and
  // thus any configuration stored in the container itself is not available
  // yet. These lines force the container cache to use Redis rather than the
  // default SQL cache.
  $settings['bootstrap_container_definition'] = [
    'parameters' => [],
    'services' => [
      'redis.factory' => [
        'class' => 'Drupal\redis\ClientFactory',
      ],
      'cache.backend.redis' => [
        'class' => 'Drupal\redis\Cache\CacheBackendFactory',
        'arguments' => ['@redis.factory', '@cache_tags_provider.container', '@serialization.phpserialize'],
      ],
      'cache.container' => [
        'class' => '\Drupal\redis\Cache\PhpRedis',
        'factory' => ['@cache.backend.redis', 'get'],
        'arguments' => ['container'],
      ],
      'cache_tags_provider.container' => [
        'class' => 'Drupal\redis\Cache\RedisCacheTagsChecksum',
        'arguments' => ['@redis.factory'],
      ],
      'serialization.phpserialize' => [
        'class' => 'Drupal\Component\Serialization\PhpSerialize',
      ],
    ],
  ];
}

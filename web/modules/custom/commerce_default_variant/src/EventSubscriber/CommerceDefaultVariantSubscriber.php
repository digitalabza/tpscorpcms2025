<?php

namespace Drupal\commerce_default_variant\EventSubscriber;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_product\Event\ProductDefaultVariationEvent;
use Drupal\commerce_product\Event\ProductEvents;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_stock\StockServiceManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to PRODUCT_DEFAULT_VARIATION to pick an in-stock variant.
 */
class CommerceDefaultVariantSubscriber implements EventSubscriberInterface
{

    /**
     * @var \Drupal\Core\Entity\EntityStorageInterface
     */
    protected $variationStorage;

    /**
     * @var \Drupal\commerce_stock\StockServiceManagerInterface
     */
    protected $stockServiceManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructs the subscriber.
     *
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface    $entity_type_manager
     *   The entity type manager.
     * @param \Drupal\commerce_stock\StockServiceManagerInterface $stock_service_manager
     *   The commerce_stock service manager.
     * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface   $logger_factory
     *   The logger factory.
     */
    public function __construct(
        EntityTypeManagerInterface $entity_type_manager,
        StockServiceManagerInterface $stock_service_manager,
        LoggerChannelFactoryInterface $logger_factory
    ) {
        $this->variationStorage    = $entity_type_manager->getStorage('commerce_product_variation');
        $this->stockServiceManager = $stock_service_manager;
        // Commented out: we’re no longer logging.
        // $this->logger = $logger_factory->get('commerce_default_variant');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // After core’s default (weight -100).
        return [
            ProductEvents::PRODUCT_DEFAULT_VARIATION => ['setDefaultVariationInStock', -100],
        ];
    }

    /**
     * Picks the first variation with real stock > 0.
     *
     * @param \Drupal\commerce_product\Event\ProductDefaultVariationEvent $event
     *   The event.
     */
    public function setDefaultVariationInStock(ProductDefaultVariationEvent $event)
    {
        $product = $event->getProduct();
        // Reload to get fresh computed fields.
        $ids = array_map(function ($v) {
            return $v->id();
        }, $product->getVariations());
        $variations = $this->variationStorage->loadMultiple($ids);

        foreach ($variations as $variation) {
            if (!($variation instanceof PurchasableEntityInterface)) {
                continue;
            }

            // Get the “true” stock level.
            $stock = $this->stockServiceManager->getStockLevel($variation);

            // Commented out: logging disabled.
            // $this->logger->info('Variation @vid of product @pid stock: @stock', [
            //   '@vid'   => $variation->id(),
            //   '@pid'   => $product->id(),
            //   '@stock' => $stock,
            // ]);

            if ((float) $stock > 0) {
                $event->setDefaultVariation($variation);
                return;
            }
        }
        // None in stock → keep core’s default.
    }
}

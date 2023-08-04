<?php

namespace Drupal\commercevariant\EventSubscriber;

use Drupal\commerce_product\Event\ProductDefaultVariationEvent;
use Drupal\commerce_product\Event\ProductEvents;
use Drupal\commerce_product\Plugin\views\argument_default\ProductVariation;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Commerce Default Variant Override event subscriber.
 */
class CommercevariantSubscriber implements EventSubscriberInterface {


  // public function __construct() {

  // }

  /**
   * {@inheritdoc}
   */


   static function getSubscribedEvents()
   {return [
    ProductEvents::PRODUCT_DEFAULT_VARIATION => ['setDefaultVariationInStock', -100],];
  }

  /**
   * @param \Drupal\commerce_product\Event\ProductDefaultVariationEvent $event
   *
   * Set default variation the first one that is not out of stock, or the last variation
   * if all out of stock
   */
  public function setDefaultVariationInStock(ProductDefaultVariationEvent $event){
    if ($event->getProduct()->bundle() == 'clothing'){
      $variations = $event->getProduct()->getVariations();
      $defaultVariation = null;
      $firstVariation = null;

      /** @var ProductVariation $variation */

      foreach ($variations as $variation) {
        $defaultVariation = $variation;
        if ($variation->hasField('field_soh') && $variation->field_soh->value != NULL)
        {$stock = (integer) $variation->field_soh->value;
	  \Drupal::logger('pricevariation')->info('Var stock '.$stock);

          if ($stock <= 0) {
            continue;
          }
          break;
        }
      }
      if ($defaultVariation!=null){
      $skudefaultVariation = $defaultVariation->getSku();
        $event->setDefaultVariation($defaultVariation);
      \Drupal::logger('pricevariation')->info('Var default '.$skudefaultVariation);

    }
  }
}

}

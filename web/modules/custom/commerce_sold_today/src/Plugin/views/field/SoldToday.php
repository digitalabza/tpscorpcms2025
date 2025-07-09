<?php

namespace Drupal\commerce_sold_today\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Provides a custom field that indicates if a product variation was sold today.
 *
 * @ViewsField("sold_today_field")
 */
class SoldToday extends FieldPluginBase
{
    /**
     * Override query() so this field doesn't alter the main view query.
     */
    public function query()
    {
        // Do nothing.
    }

    /**
     * Render the field.
     *
     * Displays whether the current product variation was sold today.
     * Shows sales only for the specific variation of the current row.
     *
     * @param \Drupal\views\ResultRow $row
     *   The view result row, where $row->_entity is the Commerce product or variation.
     *
     * @return string
     *   A string showing "SKU: Yes (N)" (where N is total units sold) for sold variations,
     *   or "No" if the variation wasn't sold.
     */
    public function render(ResultRow $row)
    {
        // Retrieve the entity from the current row
        $entity = $row->_entity;

        // Get current variation and SKU
        $variation_id = NULL;
        $sku = NULL;

        if ($entity->getEntityTypeId() == 'commerce_product_variation') {
            // If we're already dealing with a variation
            $variation_id = (int) $entity->id();
            $sku = $entity->getSku();
        } else if ($entity->getEntityTypeId() == 'commerce_product') {
            // For a product entity, find which variation this row represents
            if (
                isset($row->commerce_product_variation_field_data_commerce_product__vari) &&
                is_numeric($row->commerce_product_variation_field_data_commerce_product__vari)
            ) {
                $variation_id = (int) $row->commerce_product_variation_field_data_commerce_product__vari;
                $variation = \Drupal::entityTypeManager()
                    ->getStorage('commerce_product_variation')
                    ->load($variation_id);
                if ($variation) {
                    $sku = $variation->getSku();
                }
            }
        }

        // If we couldn't determine the variation ID or SKU, return No
        if (empty($variation_id) || empty($sku)) {
            return 'No';
        }

        // Establish a database connection
        $connection = \Drupal::database();

        // Calculate today's time range using the site's timezone
        $timezone = \Drupal::config('system.date')->get('timezone.default') ?: 'UTC';
        $now = new \DateTime('now', new \DateTimeZone($timezone));
        $start = $now->setTime(0, 0, 0)->getTimestamp(); // Midnight today in site timezone
        $end = $now->setTime(23, 59, 59)->getTimestamp(); // 23:59:59 today in site timezone

        // Build query for this specific variation
        $query = $connection->select('commerce_order_item', 'oi');
        $query->join('commerce_order', 'o', 'oi.order_id = o.order_id');
        $query->condition('oi.purchased_entity', $variation_id);
        $query->condition('o.state', 'completed');
        $query->condition('o.changed', [$start, $end], 'BETWEEN');
        $query->addExpression('SUM(oi.quantity)', 'total_sold');

        // Execute query
        $total_sold = (int) $query->execute()->fetchField() ?: 0;

        // Return sales information
        // if ($total_sold > 0) {
        //     return "{$sku}: Yes ({$total_sold})";
        // } else {
        //     return 'No';
        // }

        if ($total_sold > 0) {
            return "{$total_sold}";
        } else {
            return '0';
        }
    }
}

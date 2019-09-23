<?php

namespace App\Models;

use App\Tagplus\ProductCatSynchronize;
use TypeRocket\Exceptions\ModelException;
use \TypeRocket\Models\WPPost;

/**
 * Class Product
 *
 * @property integer from_id
 * @package  App\Models
 */
class Product extends WPPost implements Synchronize
{
    protected $postType = 'product';
    protected $fromId = null;
    protected $sku = null;
    protected $name = null;
    protected $priceId = null;
    protected $price = null;

    /**
     * Get Id from ERP
     *
     * @return null
     */
    public function getFromId()
    {
        if (!$this->fromId) {
            $this->fromId = (int)$this->getFieldValue('_from_id');
        }
        return $this->fromId;
    }

    /**
     * Set Id from ERP
     *
     * @param integer $fromId Id
     *
     * @return $this
     */
    public function setFromId($fromId)
    {
        if (!$this->getFromId()) {
            $this->fromId = $fromId;
            $this->setProperty('_from_id', $fromId);
        }

        return $this;
    }

    /**
     * Get product Sku code
     *
     * @return null
     */
    public function getSku()
    {
        if (!$this->sku) {
            $this->sku = (string)$this->getFieldValue('_sku');
        }
        return $this->sku;
    }

    /**
     * Set product Sku code
     *
     * @param string $sku Sku code
     *
     * @return void
     */
    protected function setSku($sku): void
    {
        if ($this->getID()) {
            $this->sku = $sku;
            $this->setProperty('_sku', $sku);
        }
    }

    /**
     * @return int
     */
    public function getPriceId()
    {
        if (!$this->priceId) {
            $this->priceId = (float)$this->getFieldValue('from_price_id');
        }
        return $this->priceId;
    }

    /**
     * Get product name
     *
     * @return string
     */
    protected function getName()
    {
        if (!$this->name) {
            $this->name = $this->getProperty('post_title');
        }
        return $this->name;
    }

    /**
     * Set product name
     *
     * @param string $name Name
     *
     * @return void
     */
    protected function setName($name): void
    {
        if ($this->getName() !== $name) {
            $this->name = $name;
            $this->setProperty('post_title', $name);
        }
    }

    /**
     * Get product price
     *
     * @return float
     */
    protected function getPrice()
    {
        if (!$this->price) {
            $this->price = (float)$this->getFieldValue('_regular_price');
        }
        return $this->price;
    }

    /**
     * Set product price
     *
     * @param int   $priceId Product price id
     * @param float $price   Product price
     *
     * @return void
     */
    protected function setPrice($priceId, $price): void
    {
        if ($this->getPriceId() !== $priceId || $this->getPrice() !== $price) {
            $this->price = $price;
            $this->setProperty('from_price_id', $priceId);
            // TODO: Check different prices
            $this->setProperty('_regular_price', $price);
            $this->setProperty('_sale_price', $price);
            $this->setProperty('_price', $price);
        }
    }

    /**
     * Returns true if the product belongs to the reported category
     *
     * @param ProductCat $category ProductCat object
     *
     * @return boolean
     */
    protected function hasCategory($category)
    {
        $terms = get_the_terms($this->getID(), $category->getTaxonomy());
        if (is_array($terms)) {
            foreach ($terms as $term) {
                if ($term->term_id === $category->getID()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Set product category
     *
     * @param integer $fromId   Category id
     * @param string  $fromName Category name
     *
     * @return void
     */
    protected function setCategory($fromId, $fromName)
    {
        $category = (new ProductCat())->findByFromId($fromId);

        if (!$category->getID()
            || sanitize_title($category->getName()) !== sanitize_title($fromName)
        ) {
            $category = (new ProductCatSynchronize())->synchronizeFrom($fromId);
        }

        if ($category->getID()) {
            if (!$this->hasCategory($category)) {
                wp_set_post_terms(
                    $this->getID(),
                    [$category->getID()],
                    $category->getTaxonomy(),
                    true
                );
            }
        }
    }

    /**
     * @param array $fields Fields
     */
    protected function setFields($fields)
    {
        // _sku
        // _sale_price_dates_from
        // _sale_price_dates_to
        // total_sales
        // _tax_status
        // _tax_class
        // _backorders
        // _low_stock_amount
        // _sold_individually
        // _weight
        // _length
        // _width
        // _height
        // _upsell_ids
        // _crosssell_ids
        // _purchase_note
        // _default_attributes
        // _product_image_gallery
        // _price
        // _edit_lock
        // _edit_last

        $stock = (float)@$fields['qtd_revenda'];
        $stockMin = max((float)@$fields['estoque']['qtd_min'], 0);
        $stockStatus = $stock >= $stockMin
            ? 'instock'
            : 'outstock';

        $this->setField('from_last_change', @$fields['data_alteracao']);
        $this->setField('_manage_stock', 'no');
        $this->setField('_stock', $stock);
        $this->setField('_stock_status', $stockStatus);
        $this->setField('_virtual', 'no');
        $this->setField('_downloadable', 'no');
        $this->setField('_weight', (float)@$fields['peso']);
        $this->setField('_width', (float)@$fields['largura']);
        $this->setField('_height', (float)@$fields['altura']);
        $this->setField('_length', (float)@$fields['comprimento']);

        // TODO: Import image file
        // $this->setField('_thumbnail_id', ''); // imagem_principal
        // $this->setField('', ''); // imagens
    }

    /**
     * @param string $field Field to update
     * @param mixed  $value Value to set
     *
     * @return void
     */
    protected function setField($field, $value): void
    {
        if (!$this->getID()) {
            return;
        }

        $this->setProperty($field, $value);
    }

    /**
     * Get Product by Sku code
     *
     * @param string $sku Sku code
     *
     * @return Product|$this
     */
    public function findBySku($sku)
    {
        $args = [
            'post_type' => $this->postType,
            'posts_per_page' => 1,
            'fields' => 'ids',
            'post_status' => 'any',
            'meta_query' => [
                [
                    'key' => '_sku',
                    'value' => $sku,
                ],
            ],
        ];
        $posts = get_posts($args);

        if (isset($posts[0])) {
            return (new Product())->findById($posts[0]);
        } else {
            return $this;
        }
    }

    /**
     * Update a WordPress Product from TagPlus Product
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function updateFrom($fromItem)
    {
        if (!$this->getID() || $this->getSku() !== $fromItem->getSku()) {
            return false;
        }

        $this->setFromId($fromItem->getId());
        $this->setName($fromItem->getName());
        $this->setPrice($fromItem->getPriceId(), $fromItem->getPrice());

        $this->setCategory(
            @$fromItem->getField('categoria')['id'],
            @$fromItem->getField('categoria')['descricao']
        );

        $this->setFields($fromItem->getFields());

        if ($fromItem->isMarketable()) {
            if ($this->getProperty('post_status') === 'draft') {
                $this->setProperty('post_status', 'publish');
            }
        } else {
            $this->setProperty('post_status', 'trash');
        }

        try {
            $this->update();
            return true;
        } catch (ModelException $e) {
            return false;
        }
    }

    /**
     * Create a WordPress Product from TagPlus Product
     *
     * @param \App\Tagplus\Product $fromItem Product from TagPlus
     *
     * @return bool
     */
    public function createFrom($fromItem)
    {
        if ($this->getID()) {
            return false;
        }

        $this->setName($fromItem->getName());
        $this->setProperty('post_content', ' ');
        $this->setProperty('post_status', 'draft');

        try {
            $this->create();
            $this->setFromId($fromItem->getId());
            $this->setSku($fromItem->getSku());
            $this->updateFrom($fromItem);
            $this->setProperty('post_status', 'publish');
            $this->update();
            return true;
        } catch (ModelException $e) {
            return false;
        }
    }
}

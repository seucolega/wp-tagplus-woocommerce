<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Post extends WPPost
{
    protected $postType = 'post';

    public function create($fields = [])
    {
        $this->checkTheOrder();

        return parent::create($fields);
    }

    public function update($fields = [])
    {
        $this->checkTheOrder();

        return parent::update($fields);
    }

    protected function checkTheOrder()
    {
        if ($this->getProperty('post_type') === 'shop_order'
            // && (new \WC_Order($this->getID()))->payment_complete()
            && (new \WC_Order($this->getID()))->get_status() === 'completed'
        ) {
            (new \App\Tagplus\OrderSynchronize)->synchronizeTo($this->getID());
        }
    }
}

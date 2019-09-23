<?php

namespace App\Controllers;

use App\Models\Webhook;
use TypeRocket\Controllers\Controller;

class WebhookController extends Controller
{
    /**
     * @return void
     */
    public function webhook()
    {
        if ((new Webhook())->validate()) {
            $this->response->setMessage('success');
        } else {
            $this->response->setMessage('error');
        }
    }
}

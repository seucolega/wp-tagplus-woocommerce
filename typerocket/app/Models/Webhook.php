<?php
namespace App\Models;

use App\Tagplus\Tagplus;

class Webhook
{
    public function validate()
    {
        /*
        {
          "id": 999,
          "sistema": "meusistema",
          "uid": "5cd74b6782d706dc9134f863b65bea8e1a32e8a2",
          "event_type": "pedido_criado",
          "data": [
            {"itemId":"1"}
          ]
        }
        produto: criado, alterado, apagado
        */

        if (@$_SERVER['HTTP_X_HUB_SECRET'] !== Tagplus::WEB_HOOK_SECRET) {
            return false;
        }

        $content = json_decode(file_get_contents("php://input"));

        if (!$this->validateId($content->id)) {
            return false;
        }

        $eventAndAction = explode('_', $content->event_type);
        $event = @$eventAndAction[0];
        $action = @$eventAndAction[1];
        $itemId = @$content->data[0]->id;

        if (!$event || !$action || !$itemId) {
            return false;
        }

        if ($event === 'produto') {
            (new \App\Tagplus\ProductSynchronize())
                ->synchronizeFrom($itemId, false);
        }

        return true;
    }

    /**
     * @param integer $hookId Webhook Id
     *
     * @return bool
     */
    protected function validateId($hookId)
    {
        if ($hookId <= Config::getOption('last_webhook')) {
            return false;
        }

        Config::setOption('last_webhook', $hookId);
        return true;
    }
}

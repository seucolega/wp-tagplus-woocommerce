<?php

namespace App\Tagplus;

use App\Models\User;
use TypeRocket\Exceptions\ModelException;

class CustomerSynchronize implements Synchronize
{
    use SynchronizeActions;

    public function synchronizeFrom($wpId)
    {
    }

    /**
     * @param User $userWp User object
     *
     * @return User[]
     */
    protected function fetchItems($userWp)
    {
        $items = [];

        if ($userWp->getID()) {
            $items[] = $userWp;
        }
        // } elseif ($userWp === null) {
        //     // pegar todos
        // }

        return $items;
    }

    /**
     * @param $userWpSync
     *
     * @return User|null
     */
    public function synchronizeTo($userWpSync)
    {
        $usersWp = $this->fetchItems($userWpSync);

        /* @var \App\Models\User $userWp */
        foreach ($usersWp as $userWp) {
            if (!$userWp->getFromId()) {
                $customerErp = (new Customer(null))
                    ->setDocument($userWp->getDocument())
                    ->findByDocument();
                if ($customerErp && $customerErp->getId()) {
                    try {
                        $userWp
                            ->setFromId($customerErp->getId())
                            ->update();
                    } catch (ModelException $e) {
                    }
                }
            }
            if ($userWp->getFromId()) {
                $toReturn = $this->updateTo($userWp);
            } else {
                $toReturn = $this->createTo($userWp);
            }
        }

        return isset($toReturn) && count($usersWp) === 1
            ? $toReturn
            : null;
    }

    /**
     * @param \App\Models\User $userWp User object
     *
     * @return \App\Models\User
     */
    public function createTo($userWp)
    {
        $response = (new Customer())
            ->setStatus(true)
            ->setFromUserWp($userWp)
            ->create();

        /* @var $customerErp Customer */
        $customerErp = (new Order($response['response']));

        try {
            $userWp
                ->setFromId($customerErp->getId())
                ->update();

            return $userWp;
        } catch (ModelException $e) {
        }

        return null;
    }

    /**
     * @param \App\Models\User $userWp User object
     *
     * @return \App\Models\User
     */
    public function updateTo($userWp)
    {
        return null;
    }
}

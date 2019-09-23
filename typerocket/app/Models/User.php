<?php

namespace App\Models;

use TypeRocket\Models\WPUser;

class User extends WPUser
{
    protected $fromId = null;
    protected $personType = null;
    protected $name = null;
    protected $document = null;
    protected $documentType = null;
    protected $contacts = null;
    protected $addresses = null;

    // public function getPersonType()
    // {
    //     if (!$this->personType) {
    //         $personType = $this->getFieldValue('billing_persontype');
    //         $this->personType = min(max($personType, 0), 2);
    //     }
    //
    //     return $this->personType;
    // }

    // public function getDocumentType()
    // {
    //     $documentType = $this->getPersonType() === 1 ? 'cpf' : 'cnpj';
    //     return $documentType;
    // }

    public function getDocument()
    {
        if (!$this->document) {
            // $document = $this->getFieldValue(
            //     'billing_' . $this->getDocumentType()
            // );
            $cpf = trim($this->getFieldValue('billing_cpf'));
            $cnpj = trim($this->getFieldValue('billing_cnpj'));
            if ($cpf) {
                $this->setDocument($cpf);
            } elseif ($cnpj) {
                $this->setDocument($cnpj);
            }
        }

        return $this->document;
    }

    // public function getFormattedDocument()
    // {
    //     if ($this->getDocumentType() === 'cpf') {
    //         $document = Cpf::createFromString($this->getDocument());
    //     } else {
    //         $document = Cnpj::createFromString($this->getDocument());
    //     }
    //
    //     if (false === $document) {
    //         return null;
    //     } else {
    //         return $document->format();
    //     }
    // }

    public function setDocument($document)
    {
        $document = preg_replace('/[^0-9]/', '', $document);
        $this->document = $document;
        $this->documentType = strlen($document) === 11 ? 'cpf' : 'cnpj';

        return $this;
    }

    /**
     * Get Product by Sku code
     *
     * @param string $fromId Id from ERP
     *
     * @return Product|$this
     */
    public function findByFromId($fromId)
    {
        $args = [
            'number' => 1,
            'fields' => ['ID'],
            'meta_query' => [
                [
                    'key' => '_from_id',
                    'value' => $fromId,
                ],
            ],
        ];
        $users = get_posts($args);

        if (isset($users[0])) {
            return (new User())->findById($users[0]);
        } else {
            return $this;
        }
    }

    /**
     * Get Id from ERP
     *
     * @return null
     */
    public function getFromId()
    {
        if (!$this->fromId) {
            $this->fromId = (string)$this->getFieldValue('_from_id');
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
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $this->setName(
                sprintf(
                    '%s %s',
                    $this->getFieldValue('billing_first_name'),
                    $this->getFieldValue('billing_last_name')
                )
            );
        }

        return $this->name;
    }

    /**
     * @param string $name Name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    // /**
    //  * @param string $documentType
    //  *
    //  * @return User
    //  */
    // public function setDocumentType($documentType)
    // {
    //     $this->documentType = $documentType;3
    //
    //     return $this;
    // }

    // /**
    //  * @return array
    //  */
    // public function getContacts()
    // {
    //     return $this->contacts;
    // }
    //
    // /**
    //  * @param array $contacts
    //  *
    //  * @return User
    //  */
    // public function setContacts($contacts)
    // {
    //     $this->contacts = $contacts;
    //
    //     return $this;
    // }
    //
    // /**
    //  * @return null
    //  */
    // public function getAddresses()
    // {
    //     try {
    //         $customerWc = (new \WC_Customer($this->getID()));
    //         $customerWc->get_billing_postcode();
    //         $customerWc->get_billing_address_1();
    //         $customerWc->get_billing_address_2();
    //         $customerWc->get_billing_city();
    //         $customerWc->get_billing_state();
    //     } catch (\Exception $e) {
    //     }
    //
    //     return $this->addresses;
    // }
    //
    // /**
    //  * @param array $addresses
    //  *
    //  * @return User
    //  */
    // public function setAddresses($addresses)
    // {
    //     $this->addresses = $addresses;
    //
    //     return $this;
    // }
}

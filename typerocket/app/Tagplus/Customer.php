<?php

namespace App\Tagplus;

use App\Models\User;
use Brazanation\Documents\Cnpj;
use Brazanation\Documents\Cpf;

class Customer extends Model
{
    protected $pathToFetch = 'clientes';
    protected $id = null;
    protected $status = null;
    protected $name = null;
    // protected $fancyName = null;
    protected $document = null;
    protected $documentType = null;
    // protected $documentCpf = null;
    // protected $documentCnpj = null;
    // protected $documentIe = null;
    // protected $documentIm = null;
    protected $contacts = null;
    protected $addresses = null;

    function __construct($response = null)
    {
        parent::__construct($response);

        if (is_array($response)) {
            if ($response['tipo'] === 'F') {
                $this->setDocument($response['cpf']);
            } else {
                $this->setDocument($response['cnpj']);
            }
        }
    }

    /**
     * @return string[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param string[] $contacts
     *
     * @return Customer
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param string[] $addresses
     *
     * @return Customer
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name Social name
     *
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }

    // public function setType($type)
    // {
    //     if ($type === 'F') {
    //         $this->setDocumentType('cpf');
    //         $this->setDocument()
    //     } else {
    //         $this->setDocumentType('cnpj');
    //
    //         $this->documentType = 'cnpj';
    //
    //     }
    //
    //     return $this;
    // }

    public function setDocument($document)
    {
        $this->document = preg_replace('/[^0-9]/', '', $document);

        if (!$this->getDocumentType() && $this->document) {
            $this->setDocumentType();
        }

        return $this;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param string $documentType Document type
     *
     * @return Customer
     */
    public function setDocumentType($documentType = null)
    {
        if (!$documentType && $this->document) {
            $documentType = strlen($this->document) === 11 ? 'cpf' : 'cnpj';
        }

        $this->documentType = $documentType;

        return $this;
    }

    public function getFormattedDocument()
    {
        if ($this->getDocumentType() === 'cpf') {
            $document = Cpf::createFromString($this->getDocument());
        } else {
            $document = Cnpj::createFromString($this->getDocument());
        }

        if (false === $document) {
            return null;
        } else {
            return $document->format();
        }
    }

    /**
     * @param string $document
     *
     * @return $this
     */
    public function findByDocument($document = null)
    {
        if ($document === null) {
            $document = $this->getFormattedDocument();
        }
        if (!$document) {
            return null;
        }

        $query = [
            $this->getDocumentType() => $document,
        ];
        $items = $this->fetch($query);

        if (isset($items[0])) {
            return $items[0];
        } else {
            return null;
        }
    }

    // /**
    //  * @return array
    //  */
    // protected function getContactsToRequest()
    // {
    //     $toRequest = [];
    //
    //     $toRequest[] = [
    //     ];
    //
    //     return $toRequest;
    // }
    //
    // /**
    //  * @return array
    //  */
    // protected function getAddressesToRequest()
    // {
    //     $toRequest = [];
    //
    //     $toRequest[] = [
    //     ];
    //
    //     return $toRequest;
    // }

    protected function getDataToRequest()
    {
        $data = [
            'razao_social' => $this->getName(),
            'ativo' => $this->getStatus(),
            'tipo' => $this->getDocumentType() === 'cpf' ? 'F' : 'J',
            'exterior' => false,
            $this->getDocumentType() => $this->getFormattedDocument(),
            // 'contatos' => $this->getContactsToRequest(),
            'contatos' => $this->getContacts(),
            // 'enderecos' => $this->getAddressesToRequest(),
            'enderecos' => $this->getAddresses(),
        ];

        return $data;
    }

    public function create()
    {
        return (new Tagplus())
            ->post($this->pathToFetch, $this->getDataToRequest());
    }

    /**
     * @param User $userWp User object
     *
     * @return $this
     */
    public function setFromUserWp($userWp)
    {
        $contacts = [];

        if ($userWp->getFieldValue('billing_phone')) {
            $contacts[] = [
                'descricao' => $userWp->getFieldValue('billing_phone'),
                // 'tipo_contato' => 1,
                // 'tipo_cadastro' => 1,
                // 'detalhes' => '',
                'principal' => true,
            ];
        }

        if ($userWp->getFieldValue('billing_cellphone')) {
            $contacts[] = [
                'descricao' => $userWp->getFieldValue('billing_cellphone'),
                // 'tipo_contato' => 1,
                // 'tipo_cadastro' => 1,
                // 'detalhes' => '',
                'principal' => false,
            ];
        }

        $addresses = [];

        if ($userWp->getFieldValue('billing_postcode')) {
            $addresses[] = [
                'cep' => $userWp->getFieldValue('billing_postcode'),
                'logradouro' => $userWp->getFieldValue('billing_address_1'),
                'numero' => $userWp->getFieldValue('billing_number'),
                'bairro' => $userWp->getFieldValue('billing_neighborhood'),
                'principal' => true,
                'complemento' => $userWp->getFieldValue('billing_address_1'),
                // 'pais' => '',
                // 'informacoes_adicionais' => '',
                // 'tipo_cadastro' => 1,
            ];
        }

        if ($userWp->getFieldValue('shipping_postcode')) {
            $addresses[] = [
                'cep' => $userWp->getFieldValue('shipping_postcode'),
                'logradouro' => $userWp->getFieldValue('shipping_address_1'),
                'numero' => $userWp->getFieldValue('shipping_number'),
                'bairro' => $userWp->getFieldValue('shipping_neighborhood'),
                'principal' => false,
                'complemento' => $userWp->getFieldValue('shipping_address_1'),
                // 'pais' => '',
                // 'informacoes_adicionais' => '',
                // 'tipo_cadastro' => 1,
            ];
        }

        $this
            ->setName($userWp->getName())
            ->setDocument($userWp->getDocument())
            // ->setContacts($userWp->getContacts())
            ->setContacts($contacts)
            // ->setAddresses($userWp->getAddresses())
            ->setAddresses($addresses);

        return $this;
    }
}

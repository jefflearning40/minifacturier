<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numberInvoice = null;

    #[ORM\Column(length: 255)]
    private ?string $descriptionItem = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $priceItem = null;

    #[ORM\Column]
    private ?int $qty = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $saleDate = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?seller $seller = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberInvoice(): ?string
    {
        return $this->numberInvoice;
    }

    public function setNumberInvoice(string $numberInvoice): static
    {
        $this->numberInvoice = $numberInvoice;

        return $this;
    }

    public function getDescriptionItem(): ?string
    {
        return $this->descriptionItem;
    }

    public function setDescriptionItem(string $descriptionItem): static
    {
        $this->descriptionItem = $descriptionItem;

        return $this;
    }

    public function getPriceItem(): ?string
    {
        return $this->priceItem;
    }

    public function setPriceItem(string $priceItem): static
    {
        $this->priceItem = $priceItem;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): static
    {
        $this->qty = $qty;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getSaleDate(): ?\DateTime
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTime $saleDate): static
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getSeller(): ?seller
    {
        return $this->seller;
    }

    public function setSeller(?seller $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    public function getCustomer(): ?customer
    {
        return $this->customer;
    }

    public function setCustomer(?customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}

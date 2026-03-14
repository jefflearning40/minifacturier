<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
class Seller
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstNameSeller = null;

    #[ORM\Column(length: 100)]
    private ?string $lastNameSeller = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneSeller = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'seller')]
    private Collection $invoices;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstNameSeller(): ?string
    {
        return $this->firstNameSeller;
    }

    public function setFirstNameSeller(string $firstNameSeller): static
    {
        $this->firstNameSeller = $firstNameSeller;

        return $this;
    }

    public function getLastNameSeller(): ?string
    {
        return $this->lastNameSeller;
    }

    public function setLastNameSeller(string $lastNameSeller): static
    {
        $this->lastNameSeller = $lastNameSeller;

        return $this;
    }

    public function getPhoneSeller(): ?string
    {
        return $this->phoneSeller;
    }

    public function setPhoneSeller(?string $phoneSeller): static
    {
        $this->phoneSeller = $phoneSeller;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setSeller($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getSeller() === $this) {
                $invoice->setSeller(null);
            }
        }

        return $this;
    }
}

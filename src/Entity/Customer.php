<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstNameCustomer = null;

    #[ORM\Column(length: 100)]
    private ?string $lastNameCustomer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressCustomer = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneCustomer = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'customer')]
    private Collection $invoices;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstNameCustomer(): ?string
    {
        return $this->firstNameCustomer;
    }

    public function setFirstNameCustomer(string $firstNameCustomer): static
    {
        $this->firstNameCustomer = $firstNameCustomer;

        return $this;
    }

    public function getLastNameCustomer(): ?string
    {
        return $this->lastNameCustomer;
    }

    public function setLastNameCustomer(string $lastNameCustomer): static
    {
        $this->lastNameCustomer = $lastNameCustomer;

        return $this;
    }

    public function getAddressCustomer(): ?string
    {
        return $this->addressCustomer;
    }

    public function setAddressCustomer(?string $addressCustomer): static
    {
        $this->addressCustomer = $addressCustomer;

        return $this;
    }

    public function getPhoneCustomer(): ?string
    {
        return $this->phoneCustomer;
    }

    public function setPhoneCustomer(?string $phoneCustomer): static
    {
        $this->phoneCustomer = $phoneCustomer;

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
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }
}

# MiniFacturier

## Description

MiniFacturier est une application web développée avec Symfony permettant de gérer :

* les clients
* les vendeurs
* les factures
* les produits associés aux factures

---

## Structure de la base de données

### Table `invoice`

Contient les informations générales d’une facture :

* id
* number_invoice
* sale_date
* seller_id
* customer_id

### Table `invoice_item`

Contient les lignes de produits d’une facture :

* product_name
* brand
* price
* quantity
* total
* invoice_id (clé étrangère)

---

## Relations

* Une facture peut contenir plusieurs produits
* Un produit appartient à une seule facture

```
Invoice (1) → (N) InvoiceItem
```

---

## Technologies utilisées

* PHP 8
* Symfony
* Doctrine ORM
* MySQL (Laragon / HeidiSQL)
* Bootstrap
* Font Awesome

---

## Fonctionnalités

* Gestion des clients
* Gestion des vendeurs
* Création de factures
* Association de plusieurs produits à une facture

---

## Installation

```bash
git clone <repo>
cd minifacturier
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start
```

---

## Auteur Jean Francois

Projet réalisé dans le cadre d’une formation développeur web.

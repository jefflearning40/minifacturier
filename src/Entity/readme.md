# MiniFacturier

## Description

Application web développée avec Symfony permettant de gérer :

* les clients
* les vendeurs
* les factures
* les produits associés aux factures

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
* invoice_id

## Relations

Une facture peut contenir plusieurs produits.

```
Invoice (1) → (N) InvoiceItem
```

## Technologies utilisées

* PHP
* Symfony
* Doctrine ORM
* MySQL
* Bootstrap

## Fonctionnalités

* Gestion des clients
* Gestion des vendeurs
* Création de factures
* Ajout de plusieurs produits à une facture
* Calcul du total de la facture
* statistiques :nombre de ventes et montant par vendeurs
* statistiques :nombre de clients
* statistiques: quantité vendu d'un produit
* catalogue :listes des produits et leur prix

## Installation

```
git clone <repo>
composer install
php bin/console doctrine:migrations:migrate
symfony server:start
```

## Auteur

Projet réalisé dans le cadre d’une formation développeur web.

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

### Table `customer`

Contient les informations des clients :

* id
* firstname
* lastname
* email

### Table `seller`

Contient les informations des vendeurs :

* id
* firstname
* lastname

## Relations

* Un client peut avoir plusieurs factures
* Un vendeur peut avoir plusieurs factures
* Une facture peut contenir plusieurs produits
* Un produit appartient à une seule facture

```
Customer (1) → (N) Invoice
Seller   (1) → (N) Invoice
Invoice  (1) → (N) InvoiceItem
```

## Explication technique

Au départ, les informations des produits (nom, prix, quantité) étaient stockées directement dans la table `invoice`.

Ce modèle posait plusieurs problèmes :

* duplication des données
* impossibilité d’avoir plusieurs produits par facture
* manque de flexibilité

La solution mise en place consiste à normaliser la base de données en séparant les produits dans une table dédiée `invoice_item`.

Chaque facture est liée à plusieurs lignes de produits via une relation **OneToMany** :

* `Invoice` possède une collection de `InvoiceItem`
* `InvoiceItem` possède une relation **ManyToOne** vers `Invoice`

Cela permet :

* d’ajouter plusieurs produits à une facture
* de calculer dynamiquement le total
* d’éviter les incohérences de données

## Technologies utilisées

* PHP
* Symfony
* Doctrine ORM
* MySQL
* Bootstrap et bootstrap icons et Fontawesome

## Fonctionnalités

* Gestion des clients
* Gestion des vendeurs
* Création de factures
* Ajout de plusieurs produits à une facture
* Calcul du total de la facture
* Statistiques : nombre de ventes et montant par vendeur
* Statistiques : nombre de clients
* Statistiques : quantité vendue d’un produit
* Catalogue : liste des produits et leur prix

## Installation (environnement Laragon)

1. Placer le projet dans le dossier :

```
C:\laragon\www\
```

2. Démarrer Laragon

3. Accéder au projet via le navigateur :

```
http://minifacturier.test
```

4. Installer les dépendances :

```
composer install
```

5. Créer la base de données :

```
php bin/console doctrine:database:create
```

6. Exécuter les migrations :

```
php bin/console doctrine:migrations:migrate
```

7. Lancer le serveur Symfony (optionnel) :

```
symfony server:start
```

## Auteur

Projet réalisé dans le cadre d’une formation développeur web.

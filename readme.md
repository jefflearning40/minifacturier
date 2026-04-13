# MiniFacturier

## Présentation

MiniFacturier est une application web développée avec Symfony permettant la gestion complète de facturation.  
Elle permet de gérer les clients, les vendeurs, ainsi que la création et le suivi de factures composées de plusieurs produits.

Ce projet repose sur une architecture MVC et met en œuvre une base de données relationnelle optimisée via Doctrine ORM.

---

## Fonctionnalités principales

- Gestion des clients (CRUD)  
- Gestion des vendeurs (CRUD)  
- Création et gestion de factures  
- Ajout de plusieurs produits par facture  
- Calcul automatique du total  
- Statistiques :
  - chiffre d’affaires par vendeur  
  - nombre de clients  
  - quantité vendue par produit  
- Catalogue des produits  

---

## Architecture technique

Le projet est structuré selon les bonnes pratiques Symfony :

- Controllers : gestion des routes et logique applicative  
- Entities : modélisation des données  
- Repositories : accès aux données  
- Forms : gestion des formulaires  
- Templates : rendu avec Twig  

---

## Modélisation de la base de données

La base de données est normalisée afin d’assurer cohérence et évolutivité.

### Relations principales

Customer (1) → (N) Invoice  
Seller   (1) → (N) Invoice  
Invoice  (1) → (N) InvoiceItem  

### Organisation

- Une facture est liée à un client et un vendeur  
- Une facture contient plusieurs lignes de produits (`invoice_item`)  
- Chaque ligne correspond à un produit avec quantité et prix  

Cette structure permet :

- d’ajouter plusieurs produits à une facture  
- de calculer dynamiquement le total  
- d’assurer la cohérence des données  

---

## Technologies utilisées

- PHP 8  
- Symfony  
- Doctrine ORM  
- MySQL  
- Twig  
- Bootstrap  
- Bootstrap Icons  
- Font Awesome  
- KnpPaginatorBundle:pour avoir de la pagination pour les listes templates/../index.html.twig

---

## Installation

### Prérequis

- PHP 8+  
- Composer  
- MySQL  
- Environnement local (Laragon recommandé)  

### Étapes

composer install  

php bin/console doctrine:database:create  

php bin/console doctrine:migrations:migrate  

symfony server:start  

Accès :

http://minifacturier.test  

---

## Bonnes pratiques mises en œuvre

- Architecture MVC  
- Organisation du code avec une séparation claire entre logique, données et affichage
- Utilisation de Doctrine ORM  
- Validation des données  
- Code structuré et maintenable  

---

## Axes d’amélioration

- Mise en place d’une authentification sécurisée  
- Export PDF des factures  
- Amélioration de l’ergonomie de l’interface  
- Mise en place de tests pour améliorer la fiabilité de l’application  

---

## Auteur

Projet réalisé dans le cadre d’une formation développeur web.
# MiniFacturier

## Présentation du projet

MiniFacturier est une application web développée avec Symfony permettant de gérer :

* les clients
* les vendeurs
* les factures
* un catalogue de produits

L’objectif est de proposer une interface simple et efficace pour une activité commerciale.

---

## Objectifs pédagogiques

Ce projet a été réalisé dans le cadre de la préparation aux certifications CCP1 et CCP2.

Il permet de mettre en pratique :

* la création d’une application web complète
* la gestion des bases de données
* la sécurisation des accès (rôles)
* la mise en place d’un back-office professionnel

---

## Technologies utilisées

* PHP 8
* Symfony
* Doctrine ORM
* Twig
* Bootstrap 5
* MySQL
* KnpPaginatorBundle

---

## Fonctionnalités principales

### Gestion des utilisateurs

* Authentification sécurisée
* Gestion des rôles :

  * Administrateur
  * Vendeur

---

### Gestion des entités

#### Clients

* Création, modification et suppression
* Liste paginée

#### Vendeurs

* CRUD complet
* Association avec un utilisateur

#### Factures

* Création de factures
* Calcul automatique HT / TVA / TTC
* Association client et vendeur

#### Produits (Catalogue)

* CRUD complet (admin uniquement)
* Catalogue visible par tous les utilisateurs

---

## Catalogue produit

Le catalogue est une partie centrale de l’application.

### Fonctionnalités :

* Pagination (10 produits par page)
* Recherche par nom (multi-mots, insensible à la casse)
* Filtre par marque
* Combinaison recherche + filtre
* Actions réservées à l’administrateur (modifier, supprimer)

---

## Statistiques

### Administrateur

* Nombre de vendeurs
* Nombre de clients
* Nombre de factures
* Chiffre d’affaires global
* Produit le plus vendu
* Produit le moins vendu
* Tableau des ventes par produit

### Vendeur

* Nombre de factures
* Total des ventes
* Nombre de produits vendus
* Répartition des ventes par produit

---

## Interface utilisateur

* Interface responsive avec Bootstrap
* Navigation claire et structurée
* Système de composants réutilisables :

  * boutons CRUD (voir, modifier, supprimer)
  * boutons de navigation (précédent, suivant)
* Uniformisation du design sur l’ensemble du projet

---

## Sécurité

* Gestion des rôles avec `is_granted`
* Protection des routes sensibles
* Accès restreint aux fonctionnalités administrateur

---

## Organisation du code

* Architecture MVC avec Symfony
* Utilisation des Repository pour les requêtes complexes
* Séparation des responsabilités :

  * affichage (Twig)
  * logique métier (Controller)
  * accès aux données (Repository)

---

## Améliorations possibles

* Tri des produits (prix, nom) pour afficher selon le prix ou le nom du produit
* Export PDF des factures pour pouvoir les enregistrer, les imprimer ou les envoyer par mail
* Ajout de graphiques statistiques pour le rendu visible et le confort visuel et l'interpretation
  plus rapide     
* Filtres avancés afin de faciliter encore plus la recherche et reduire le contenu affiché un
  resultat pluys précis.
* API REST dans le but de rendre ces données exportables dans des applications externes



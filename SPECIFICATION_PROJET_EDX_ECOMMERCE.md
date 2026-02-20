# Cahier des Charges - Projet EDX E-Commerce

## Informations Générales

**Nom du projet :** EDX E-Commerce  
**Type d'application :** Plateforme e-commerce complète  
**Technologie :** Laravel 12.x, PHP 8.2, Tailwind CSS, Alpine.js  
**Date de rédaction :** Janvier 2026  
**État actuel :** En développement (18% complété selon la roadmap)  

---

## Description du Projet

EDX E-Commerce est une plateforme e-commerce moderne conçue pour répondre aux besoins variés du commerce électronique en Afrique de l'Ouest, notamment au Togo. Le système combine plusieurs fonctionnalités : boutique e-commerce traditionnelle, espace publicitaire, partenariats d'entreprise, services locaux et marketplace.

Le projet vise à créer une plateforme tout-en-un qui permet non seulement aux clients d'acheter des produits, mais aussi aux entreprises de promouvoir leurs services, de diffuser des publicités et de collaborer à travers des partenariats.

---

## Architecture Générale

### Structure Technique
- **Backend :** Laravel 12.x avec PHP 8.2
- **Frontend :** Blade templates, Tailwind CSS, Alpine.js
- **Base de données :** MySQL/PostgreSQL (selon configuration)
- **Build tool :** Vite
- **Authentification :** Laravel Breeze
- **Architecture :** MVC avec séparation claire entre front-office et back-office

### Modèle de Sécurité
- Double système d'authentification (utilisateurs normaux vs administrateurs)
- Middleware personnalisé pour la gestion des accès
- Sessions uniques par utilisateur
- Protection contre les accès croisés entre admin et utilisateur

---

## Fonctionnalités Actuellement Implémentées

### 1. Front-Office (Client)

#### 1.1 Accueil et Navigation
- Page d'accueil avec section héro, catégories phares, promotions et métriques
- Système de bannière publicitaire rotative avec suivi des vues/clics
- Navigation optimisée avec menu déroulant géré par Alpine.js

#### 1.2 Catalogue de Produits
- Affichage des produits avec filtres (catégorie, recherche, prix min/max, en stock)
- Système de tri (prix croissant/décroissant, popularité)
- Pagination configurable
- Affichage des statistiques du catalogue (total produits, en stock)

#### 1.3 Fiche Produit
- Visualisation complète avec images multiples
- Affichage des prix (normal, promotionnel, partenaire, premium)
- Produits liés basés sur la catégorie
- Fonctionnalité de liste de souhaits

#### 1.4 Recherche et Autocomplétion
- Recherche instantanée avec suggestions
- Système de dropdown avec prévisualisation

#### 1.5 Gestion du Panier
- Panier persistant par session ou utilisateur connecté
- Ajout, mise à jour de quantité, suppression d'articles
- Calcul automatique des totaux

#### 1.6 Codes Promotionnels
- Application et retrait de codes promo
- Validation par type (pourcentage/fixe) et dates
- Intégration avec le panier et checkout

#### 1.7 Processus de Commande
- Validation des adresses
- Création de commande avec items et total avec remise
- Conversion du panier en commande "ordered"

#### 1.8 Espace Client
- Tableau de bord personnel
- Historique des derniers achats
- Gestion des adresses
- Compteur de produits en liste de souhaits

### 2. Back-Office (Administration)

#### 2.1 Authentification Admin
- Système d'authentification dédié pour les administrateurs
- Tableau de bord admin avec KPIs clés (commandes, produits, clients, revenus)

#### 2.2 Gestion des Produits
- CRUD complet pour les produits
- Upload d'images multiples avec image principale
- Gestion des prix multiples (normal, promo, partenaire, premium)
- Gestion du stock

#### 2.3 Gestion des Catégories
- CRUD pour catégories
- Hiérarchie parent/enfant possible
- Statistiques par catégorie

#### 2.4 Gestion des Clients
- Liste et détails des clients
- Blocage/déblocage des comptes
- Réinitialisation de mot de passe
- Suppression de comptes

#### 2.5 Gestion des Commandes
- Liste des commandes avec filtres
- Mise à jour des statuts (pending/confirmed/shipped/delivered/cancelled)
- Suivi logistique (transporteur/code/url)
- Génération de factures

#### 2.6 Gestion des Codes Promo
- CRUD pour les codes promotionnels
- Configuration des types (montant fixe, pourcentage)
- Dates de validité

#### 2.7 Paramètres Système
- Configuration du nom du site, logo, couleurs
- Email expéditeur
- Moyens de paiement
- Frais de port
- Activation/désactivation de fonctionnalités

### 3. Systèmes Avancés

#### 3.1 Partenariats et Entreprises
- Gestion des partenaires avec profils complets
- Articles et contenus associés aux partenaires
- Galerie d'images pour les partenaires
- Gestion des entreprises avec produits associés

#### 3.2 Publicité et Marketing
- Système de gestion des publicités
- Types de médias supportés (images, vidéos, liens)
- Suivi des vues et clics
- Différents modèles de paiement (jour/clic/mois)
- Plans publicitaires avec abonnements

#### 3.3 Marchés et Localisation
- Gestion des marchés (géographiques ou thématiques)
- Association de boutiques et produits aux marchés
- Localisation des offres

#### 3.4 Services Locaux
- Annuaire de services (mécaniciens, plombiers, etc.)
- Recherche par catégorie et lieu
- Système de notation et disponibilité

---

## Modèles de Données Principaux

### Produit
- Relations : catégorie, partenaire, marché, entreprise
- Prix multiples : normal, promo, partenaire, premium
- Stock et disponibilité
- Images multiples

### Utilisateur
- Système d'administration (is_admin)
- Blocage utilisateur (is_blocked)
- Relations : adresses, commandes, liste de souhaits

### Commande
- Suivi logistique (transporteur, code, URL)
- Champs financiers et stock
- Statut de paiement

### Publicité
- Types de média, modèles de paiement
- Suivi des performances (vues, clics)
- Dates de validité

---

## Fonctionnalités en Cours de Développement

### 1. Paiements en Ligne
- Intégration avec TMoney et Flooz (paiements mobiles locaux)
- Intégration Stripe Checkout
- Confirmation automatique et sécurité

### 2. Gestion Avancée des Stocks
- Décrémentation automatique à la confirmation de commande
- Calcul des frais de port et taxes
- Historique détaillé des commandes

### 3. Communications Automatisées
- Emails de confirmation de commande
- Factures automatiques
- Notifications d'expédition
- Rappels de panier abandonné

### 4. Améliorations Marketing
- Avis clients et systèmes de notation
- Campagnes promotionnelles avancées
- Statistiques marketing détaillées

---

## Fonctionnalités à Développer

### 1. Paiement et Finances
- [ ] Intégration complète des paiements (TMoney/Flooz/Stripe)
- [ ] Confirmation automatique des paiements
- [ ] Gestion des remboursements
- [ ] Historique financier détaillé

### 2. Expérience Client Avancée
- [ ] Système d'abandon de panier (email/SMS/push)
- [ ] Rappels de paiement
- [ ] Suivi de commande avancé
- [ ] Système d'avis et notation clients

### 3. Marketing Intelligent
- [ ] Prix promotionnels dynamiques
- [ ] Pricing partenaire et premium
- [ ] Campagnes marketing avec suivi
- [ ] Analyse des performances marketing

### 4. Publicité Avancée
- [ ] Publicités pour entreprises
- [ ] Services mis en avant
- [ ] Sponsoring de catégories
- [ ] Interface de commande publicitaire pour utilisateurs

### 5. Gestion des Marchés
- [ ] CRUD complet pour les marchés
- [ ] Association de boutiques et produits
- [ ] Localisation géographique

### 6. Espace Entreprise
- [ ] Dashboard entreprise personnalisé
- [ ] Gestion des produits/services
- [ ] Suivi des commandes
- [ ] Messagerie client
- [ ] Facturation et statistiques

### 7. Services et Annuaire
- [ ] Modèle de service complet
- [ ] Recherche par catégorie et lieu
- [ ] Filtres par disponibilité et note
- [ ] Système de réservation

### 8. Paramètres Avancés
- [ ] Feature flags (interrupteurs de fonctionnalités)
- [ ] Activation/désactivation des codes promo
- [ ] Message center (chat)
- [ ] Authentification à deux facteurs
- [ ] Notifications configurables (SMS/Push)

---

## Spécifications Techniques

### Performance
- Système de caching optimisé
- Pagination efficace
- Chargement asynchrone pour les suggestions de recherche
- Optimisation des requêtes SQL

### Sécurité
- Validation des entrées utilisateur
- Protection CSRF
- Sanitization des données
- Autorisations basées sur les rôles
- Sessions sécurisées

### Extensibilité
- Architecture modulaire
- Modèles extensibles
- Système de paramètres centralisé
- API prête pour les développements futurs

---

## Contraintes et Règles Métier

### 1. Gestion des Accès
- Les administrateurs ne peuvent pas accéder aux routes utilisateur
- Les utilisateurs ne peuvent pas accéder aux routes admin
- Sessions uniques par utilisateur (connexion déconnecte les autres sessions)

### 2. Gestion des Stocks
- Les produits doivent avoir une quantité en stock pour être affichés
- Système de notification pour les stocks faibles

### 3. Publicité
- Les publicités ont des dates de validité
- Suivi des performances (vues, clics)
- Différents modèles de paiement disponibles

### 4. Partenariats
- Les partenaires peuvent avoir des articles et produits associés
- Gestion des galeries d'images pour les partenaires
- Profils complets avec descriptions et coordonnées

---

## Risques et Points d'Attention

### 1. Risques Techniques
- Intégration des paiements mobiles locaux (TMoney/Flooz)
- Scalabilité avec la croissance des utilisateurs
- Performance avec un grand nombre de produits et commandes

### 2. Risques Fonctionnels
- Complexité du système multi-fonctionnalités
- Gestion des droits d'accès dans un système hybride
- Coordination entre les différentes parties prenantes

### 3. Points d'Optimisation
- Amélioration de la recherche (description, catégorie, prix)
- Tests automatisés pour les modules critiques
- Internationalisation pour d'autres marchés

---

## Conclusion

Le projet EDX E-Commerce représente une plateforme e-commerce ambitieuse qui combine les fonctionnalités traditionnelles d'une boutique en ligne avec des systèmes avancés de partenariats, de publicité et de services locaux. 

À ce stade (18% complété), la base solide est posée avec un système d'authentification double couche, une gestion complète des produits et commandes, ainsi qu'un système avancé de partenariats et de publicité.

Les prochaines phases devront se concentrer sur les paiements, les communications automatisées et l'amélioration continue de l'expérience utilisateur pour atteindre l'objectif d'une plateforme e-commerce complète et compétitive sur le marché africain.

Le système est bien architecturé et prêt à accueillir les fonctionnalités restantes grâce à son approche modulaire et sa séparation claire entre les différentes couches de l'application.
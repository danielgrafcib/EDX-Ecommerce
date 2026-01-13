# Roadmap du Projet EDX
Calcul de progression basé sur les fonctionnalités implémentées vs demandées.

### Progression Totale: 18%
(Estimée: 9/50 fonctionnalités majeures)

---

## 1️⃣ EDX E-COMMERCE (Cœur du système)
- [ ] **Paiements**
    - [ ] Intégration TMoney
    - [ ] Intégration Flooz
    - [ ] Intégration Stripe Checkout
    - [ ] Confirmation auto & Sécurité
- [ ] **Fonctions e-commerce clés**
    - [ ] 🛒 Abandon Cart (Email/SMS/Push)
    - [ ] 🔁 Resend / Rappel paiement
    - [x] 🎟 Codes promo (Base existante - à améliorer avec types & limites)
    - [ ] 📦 Suivi de commande avancé
    - [ ] ⭐ Avis clients

## 2️⃣ MARKETING & PRICING
- [ ] **Pricing intelligent**
    - [x] Prix normal
    - [ ] Prix promotionnel (Champs à ajouter)
    - [ ] Prix partenaire
    - [ ] Prix premium
- [ ] **Marketing intégré**
    - [x] Bannières sponsorisées (Module Publicités existant)
    - [ ] Campagnes promotionnelles
    - [ ] Statistiques marketing (clics, ventes)

## 3️⃣ PUBLICITÉ SUR EDX
- [x] Gestion des publicités (Admin)
- [ ] **Types de publicité**
    - [ ] Pub entreprises
    - [ ] Services mis en avant
    - [ ] Sponsoring catégories
- [ ] **Modèles de paiement**
    - [ ] Par jour/clic/mois
    - [ ] Interface commande pub pour les utilisateurs

## 4️⃣ MARCHÉS (Backoffice)
- [ ] **Gestion des marchés**
    - [ ] CRUD Marché (Lomé, Numérique...)
    - [ ] Association Boutiques/Produits aux marchés

## 5️⃣ ENTREPRISES & PARTENARIATS
- [x] **Page Partenaire** (Base existante : `Partner`, `PartnerArticle`)
- [ ] **Page Entreprise** (Extension de Partenaire)
- [ ] **Dashboard Entreprise**
    - [ ] Produits/Services
    - [ ] Commandes
    - [ ] Messages clients
    - [ ] Facturation/Stats

## 6️⃣ PARAMÈTRES AVANCÉS
- [x] Architecture Paramètres (Modèle `Setting` existant)
- [ ] **Feature Flags (Toggles)**
    - [ ] Activation Codes promo
    - [ ] Message Center (Chat)
    - [ ] 2FA
    - [ ] Notifications (SMS/Push configurables)

## 7️⃣ PAGE « LES SERVICES »
- [ ] **Annuaire Services**
    - [ ] Modèle Service (Mécanicien, Plombier...)
    - [ ] Recherche par catégorie/lieu
    - [ ] Filtres disponibilité/note

## 8️⃣ APPLI-DIRECTORY
- [ ] **Marketplace Apps**
    - [ ] Fiches applications
    - [ ] Plans (Simple, Standard, Premium)

## 9️⃣ INTRANET ENTREPRISE
- [ ] **Gestion interne**
    - [ ] Employés
    - [ ] Tâches/Docs

## 🔟 CONTENU & STRATÉGIE
- [ ] Page « Qu'offrons-nous ? »
- [ ] Intégration Plan Marketing (Landing pages, Blog)

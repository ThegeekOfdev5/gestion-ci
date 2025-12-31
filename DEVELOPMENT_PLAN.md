# 📋 Plan de Développement MVP - ERP OHADA Cloud (Côte d'Ivoire)

## 🎯 1. Vision du Produit

**ERP OHADA Cloud** - Une solution de gestion d'entreprise tout-en-un, simple et abordable pour les PME ivoiriennes, avec focus sur la facturation et la conformité fiscale DGI Côte d'Ivoire.

**Pitch** : "Gérez votre entreprise, facturez vos clients et déclarez votre TVA en quelques clics, conformément à la réglementation ivoirienne."

---

## 🏗️ 2. Architecture Technique

### Stack Technologique
- **Backend** : Laravel 11 + PHP 8.2
- **Frontend** : Livewire 3 + Alpine.js + Tailwind CSS
- **Admin Panel** : Filament PHP 3
- **Base de données** : PostgreSQL 15
- **Cache** : Redis 7
- **Queue** : Laravel Horizon + Redis
- **Search** : Laravel Scout (Algolia/Meilisearch)
- **PDF** : DomPDF + Snappy (fallback)
- **Email** : Mailgun/Postmark
- **Paiements** : CinetPay API (Orange CI, MTN CI, Carte)
- **Monitoring** : Sentry + Laravel Telescope
- **CI/CD** : GitHub Actions
- **Hébergement** : Serveur local Côte d'Ivoire (GVA, AFRILAND, etc.)
- **Backup** : Spatie Laravel Backup + Stockage local

### Architecture Multi-Tenant
```
Schema: Single database with tenant_id
Isolation: Middleware + Global Scopes
Data: Soft deletes + audit logs
Backup: Per-tenant export option
Conformité: Archivage 10 ans (loi ivoirienne)
```

---

## 📅 3. Timeline Détaillée (16 semaines)

### Sprint 0 : Setup & Architecture (Semaines 1-2)

#### Objectifs
- Environnement de développement opérationnel
- CI/CD configuré
- Architecture de base validée
- Conformité RGCI (Règlementation Générale de la Comptabilité Ivoirienne)

#### Tâches Techniques
```markdown
- [ ] Initialisation projet Laravel 11
- [ ] Configuration environnements (dev/staging/prod)
- [ ] Setup Git + GitHub avec branches protected
- [ ] CI/CD pipeline GitHub Actions
- [ ] Choix hébergement local CI (soumission RGCI)
- [ ] Configuration serveur
- [ ] Base de données PostgreSQL + Redis
- [ ] Installation packages essentiels
- [ ] Architecture multi-tenant (design patterns)
- [ ] Migrations de base (users, tenants, etc.)
- [ ] Seeders plan comptable OHADA adapté CI
- [ ] Setup tests unitaires (Pest)
- [ ] Docker local development
- [ ] Monitoring développement (Telescope)
- [ ] Configuration locale Côte d'Ivoire (fuseau, devise, formats)
```

#### Livrables
- ✅ Repo GitHub avec CI/CD fonctionnel
- ✅ Environnement staging accessible
- ✅ Documentation architecture
- ✅ Tests de base passants
- ✅ Conformité initiale RGCI

---

### Sprint 1 : Authentification & Tenancy (Semaines 3-4)

#### Fonctionnalités
**Système d'inscription (Onboarding)**
- [ ] Formulaire multi-étapes (entreprise → compte)
- [ ] Validation email et téléphone CI
- [ ] Choix formule (Starter/Essentiel/Business)
- [ ] Création automatique tenant
- [ ] Configuration initiale entreprise

**Authentification**
- [ ] Login/Logout sécurisé
- [ ] "Se souvenir de moi"
- [ ] Récupération mot de passe (email + SMS)
- [ ] Middleware tenant isolation
- [ ] Session management

**Gestion Profil**
- [ ] Modifier informations personnelles
- [ ] Changer mot de passe
- [ ] Upload photo profil
- [ ] Préférences utilisateur
- [ ] Validation numéro téléphone (Orange, MTN)

**Paramètres Entreprise**
- [ ] Upload logo (optimisation automatique)
- [ ] Informations légales (NIF, RCCM, ICE, IFU)
- [ ] Coordonnées complètes (Abidjan, etc.)
- [ ] Numérotation factures selon norme CI
- [ ] Paramètres TVA (18% standard, 0%, 10% selon activité)
- [ ] Régime fiscal (Réel Simplifié, Réel Normal)

#### Tests
- [ ] Tests fonctionnels inscription (10 scénarios)
- [ ] Tests isolation tenant (data leakage)
- [ ] Tests sécurité (OWASP Top 10)
- [ ] Tests performance (1000 users seed)
- [ ] Validation formats téléphone CI

---

### Sprint 2 : Gestion Tiers (Semaines 5-6)

#### Module Clients
- [ ] Liste clients avec recherche/filtres
- [ ] Formulaire ajout (Livewire modal)
- [ ] Fiche client détaillée (NIF, RCCM, ICE si entreprise)
- [ ] Modification/Suppression (soft delete)
- [ ] Import CSV (validation + mapping)
- [ ] Export Excel (format standard)
- [ ] Duplication client
- [ ] Statistiques par client
- [ ] Classification (Particulier/Entreprise)

#### Module Fournisseurs
- [ ] Liste fournisseurs
- [ ] Formulaire fournisseur
- [ ] Mêmes fonctionnalités que clients
- [ ] Tags catégorisation
- [ ] Coordonnées bancaires (pour virements)

#### Module Produits & Services
- [ ] Catalogue produits
- [ ] Formulaire produit (référence, nom, prix HT, TVA)
- [ ] Gestion stocks (quantité simple)
- [ ] Catégories produits
- [ ] Import/Export CSV
- [ ] Images produits (optimisées)
- [ ] Historique prix
- [ ] Produits/services toggle
- [ ] Code NSI (Nomenclature Statistique Internationale) si applicable

#### Tests
- [ ] CRUD complet chaque module
- [ ] Import CSV 100+ lignes
- [ ] Validation données spécifiques CI
- [ ] Performance avec 5000 enregistrements

---

### Sprint 3 : Facturation ⭐ (Semaines 7-9)

#### Création Facture
- [ ] Composant Livewire interactif
- [ ] Sélection client (searchable)
- [ ] Ajout lignes produits (autocomplete)
- [ ] Calculs automatiques (HT, TVA 18%, TTC)
- [ ] Numérotation auto selon norme CI
- [ ] Mentions obligatoires (loi ivoirienne)
- [ ] Notes/conditions personnalisables
- [ ] Enregistrement brouillon
- [ ] Validation + finalisation
- [ ] Règlement partiel/arrhes
- [ ] Acomptes (pour devis)

#### Gestion Factures
- [ ] Liste avec filtres avancés
- [ ] Recherche globale
- [ ] Pagination optimisée
- [ ] Actions rapides (voir, modifier, dupliquer)
- [ ] Statuts: brouillon/envoyée/partiellement payée/payée/annulée
- [ ] Rappels automatiques impayés
- [ ] Paiements multiples par facture

#### Détail Facture
- [ ] Affichage complet
- [ ] Timeline événements
- [ ] Actions: Email, PDF, Marquer payée, Annuler
- [ ] Ajouter paiement (espèces, virement, mobile money)
- [ ] Notes internes

#### Génération PDF
- [ ] Template professionnel conforme CI
- [ ] Logo + en-tête entreprise
- [ ] Mentions légales obligatoires CI
- [ ] QR code (optionnel mais recommandé)
- [ ] Cache PDF (performance)
- [ ] Preview avant envoi
- [ ] Filigrane "COPIE" pour duplicata

#### Email Facture
- [ ] Template email professionnel
- [ ] PDF en pièce jointe
- [ ] Suivi ouverture/clics
- [ ] Relance automatique

#### Devis
- [ ] Création similaire facture
- [ ] Conversion devis → facture
- [ ] Validité devis (jours)
- [ ] PDF devis spécifique
- [ ] Suivi devis (envoyé, accepté, refusé)

#### Comptabilité Automatique
- [ ] Écritures auto: 411 (Clients), 701 (Ventes), 44551 (TVA collectée)
- [ ] Journal des ventes
- [ ] Réconciliation simplifiée

#### Tests
- [ ] 50 factures de test (scénarios variés)
- [ ] Calculs TVA précis (arrondis selon normes CI)
- [ ] Génération PDF < 3s
- [ ] Envoi email batch
- [ ] Audit trail modifications
- [ ] Conformité légale facture CI

---

### Sprint 4 : Comptabilité de Base (Semaines 10-11)

#### Plan Comptable OHADA Adapté CI
- [ ] Affichage hiérarchique (Classes 1-8)
- [ ] Recherche rapide compte
- [ ] 250+ comptes pré-remplis adaptés CI
- [ ] Détail compte (solde, mouvements)
- [ ] Modification limitée (admin only)
- [ ] Comptes spécifiques CI (TVA 44551, etc.)

#### Journal des Ventes
- [ ] Lecture seule (MVP)
- [ ] Filtres: dates, compte, montant
- [ ] Origine chaque écriture (lien facture)
- [ ] Export Excel
- [ ] Contrôle séquence numérotation

#### Balance de Vérification
- [ ] Génération période personnalisée
- [ ] Totaux débit/crédit/solde
- [ ] Équilibrage automatique
- [ ] Export PDF imprimable
- [ ] Comparaison périodes
- [ ] Balance auxiliaire clients/fournisseurs

#### Grand Livre
- [ ] Consultation par compte
- [ ] Mouvements détaillés
- [ ] Soldes cumulés
- [ ] Filtrage avancé
- [ ] Lettrage automatique (simplifié)

#### États Financiers Simplifiés
- [ ] Compte de résultat (mois/trimestre/année)
- [ ] Bilan simplifié
- [ ] Export présentation clean
- [ ] Format compatible expert-comptable

#### Tests
- [ ] 10 000 écritures générées
- [ ] Balance toujours équilibrée
- [ ] Performance requêtes
- [ ] Intégrité données
- [ ] Conformité RGCI

---

### Sprint 5 : Déclaration TVA ⭐ (Semaines 12-13)

#### Calcul Automatique TVA CI
- [ ] TVA collectée (ventes période)
- [ ] TVA déductible (sur achats - future)
- [ ] TVA nette à payer
- [ ] Par période mensuelle/trimestrielle selon régime
- [ ] Ajustements manuels possibles
- [ ] Différents taux (18%, 10%, 0%, exonéré)

#### Formulaire DGI Côte d'Ivoire
- [ ] Reproduction fidèle formulaire officiel CI
- [ ] Remplissage automatique
- [ ] Validation champs spécifiques
- [ ] Calculs dynamiques
- [ ] Sauvegarde brouillon
- [ ] Simulation avant soumission

#### Génération PDF Déclaration
- [ ] Format exact DGI CI
- [ ] Imprimable
- [ ] Cache local
- [ ] Archivage automatique

#### Historique Déclarations
- [ ] Archives complètes
- [ ] Consultation/réimpression
- [ ] Statut: brouillon/soumise/payée
- [ ] Date soumission
- [ ] Référence paiement

#### Alertes & Rappels
- [ ] Notification 5 jours avant échéance (20 du mois suivant)
- [ ] Email rappel
- [ ] Dashboard warning
- [ ] Calendrier fiscal CI
- [ ] Jours fériés ivoiriens intégrés

#### Tests
- [ ] Scénarios TVA complexes CI
- [ ] Conformité format DGI CI
- [ ] Performance calculs
- [ ] Backup données
- [ ] Test avec différents régimes TVA

---

### Sprint 6 : Dashboard & Paiements (Semaines 14-15)

#### Dashboard Principal
- [ ] KPI Cards: CA mois, Factures impayées, TVA due, Clients actifs
- [ ] Graphique CA 6 mois (Chart.js)
- [ ] Factures récentes (last 10)
- [ ] Alertes importantes (TVA, échéances)
- [ ] Liens rapides actions
- [ ] Widgets personnalisables
- [ ] Vue géographique clients (Abidjan, etc.)

#### Intégration CinetPay CI
- [ ] Configuration API (sandbox/prod)
- [ ] Initier paiement abonnement
- [ ] Webhook traitement
- [ ] Renouvellement automatique
- [ ] Gestion échecs/retours
- [ ] Logs transactions complètes
- [ ] Support Orange Money CI, MTN Mobile Money CI, Carte
- [ ] Devise XOF (Franc CFA)

#### Gestion Abonnements
- [ ] Affichage plan actuel
- [ ] Usage (factures/mois, stockage)
- [ ] Upgrade/Downgrade
- [ ] Annulation (fin de période)
- [ ] Historique paiements
- [ ] Factures abonnement
- [ ] Tarification en XOF

#### Notifications Système
- [ ] Notifications in-app
- [ ] Emails transactionnels
- [ ] SMS notifications (optionnel)
- [ ] Préférences notification
- [ ] Mark as read/unread

#### Tests
- [ ] Flux paiement complet
- [ ] Webhooks CinetPay
- [ ] Scénarios échec
- [ ] Sécurité données
- [ ] Paiements en XOF

---

### Sprint 7 : Polish & QA (Semaine 16)

#### Optimisation Performance
- [ ] Audit requêtes N+1
- [ ] Mise en cache stratégique
- [ ] Lazy loading images
- [ ] Minification assets production
- [ ] Compression HTTP
- [ ] CDN local CI

#### Responsive Design
- [ ] Test tablette (iPad)
- [ ] Test mobile (iPhone/Android)
- [ ] Touch interactions
- [ ] Zoom/scale approprié
- [ ] Impression mobile

#### Accessibilité
- [ ] Contrastes couleurs (WCAG AA)
- [ ] Navigation clavier
- [ ] Attributs ARIA
- [ ] Screen reader testing
- [ ] Focus management

#### Sécurité Renforcée
- [ ] Audit OWASP Top 10
- [ ] Rate limiting endpoints
- [ ] CSRF protection
- [ ] XSS prevention
- [ ] SQL injection protection
- [ ] Headers sécurité
- [ ] Logs audit sensibles
- [ ] Conformité loi informatique et libertés CI

#### Tests Utilisateurs
- [ ] 5-10 bêta-testeurs locaux
- [ ] Scénarios réalistes PME ivoiriennes
- [ ] Collecte feedback
- [ ] Corrections urgentes
- [ ] Satisfaction survey

#### Documentation
- [ ] README technique
- [ ] Guide déploiement
- [ ] Base connaissances (15 articles)
- [ ] Vidéos tutoriels (français CI)
- [ ] FAQ utilisateur
- [ ] Contact support local

#### Production Ready
- [ ] Monitoring: Sentry + Logging
- [ ] Analytics: Google Analytics/Plausible
- [ ] Uptime: UptimeRobot
- [ ] Backups: Automatiques + test restore
- [ ] SSL: Certificat valide
- [ ] Email: SPF/DKIM/DMARC
- [ ] Hébergement certifié CI

#### Livrables Finaux
- ✅ Application en production
- ✅ Documentation complète
- ✅ Tests automatisés > 80%
- ✅ Performance optimisée
- ✅ Support opérationnel local
- ✅ Conformité fiscale CI validée

---

## 🎯 4. Périmètre Fonctionnel MVP

### ✅ INCLUS (Version 1.0)

#### 1. **Authentification & Comptes**
- Inscription email/mot de passe
- Validation téléphone CI
- Login sécurisé
- Récupération mot de passe
- Profil utilisateur
- Multi-utilisateurs (3 max)
- Rôles basiques (Admin/Utilisateur)

#### 2. **Gestion Entreprise CI**
- Informations société (IFU, NIF, RCCM, ICE)
- Logo entreprise
- Coordonnées contact
- Paramètres généraux
- Thème couleur marque
- Régime fiscal CI

#### 3. **Clients**
- Liste clients
- Fiche client complète (avec ICE si entreprise)
- CRUD complet
- Import CSV
- Export Excel
- Classification Particulier/Entreprise

#### 4. **Fournisseurs**
- Liste fournisseurs
- Fiche fournisseur
- CRUD complet
- Coordonnées bancaires

#### 5. **Produits & Services**
- Catalogue articles
- Fiche produit (référence, nom, prix, TVA)
- Catégories simples
- Stock basique (quantité)
- Codes TVA multiples (18%, 10%, 0%)

#### 6. **Facturation ⭐**
- Créer facture/devis conforme CI
- Convertir devis → facture
- Numérotation automatique norme CI
- Calcul TVA (18%, 10%, 0%)
- PDF professionnel DGI CI
- Envoi email client
- Statuts multiples
- Historique complet
- Mentions légales obligatoires

#### 7. **Comptabilité Simplifiée**
- Plan comptable OHADA adapté CI
- Enregistrement auto factures
- Journal des ventes
- Balance vérification
- Grand livre consultation
- Conformité RGCI

#### 8. **Déclaration TVA ⭐**
- Génération auto déclaration mensuelle/trimestrielle
- Calcul TVA collectée/déductible
- Export PDF conforme DGI CI
- Historique déclarations
- Alertes échéances (20 du mois)
- Simulation TVA

#### 9. **Tableau de Bord**
- CA du mois (en XOF)
- Factures impayées
- TVA à déclarer
- Graphique CA 6 mois
- KPIs essentiels
- Alertes conformité

#### 10. **Paiements & Abonnements**
- Intégration CinetPay CI
- Orange Money CI / MTN Mobile Money CI / Cartes
- Gestion abonnement
- Facturation automatique en XOF
- Tarification locale adaptée

#### 11. **Support Local**
- Chat intégré (Crisp/Tawk)
- WhatsApp direct
- Base connaissances français CI
- Support téléphonique local
- Contact formulaire

### ❌ HORS SCOPE MVP (Phase 2+)

#### Modules Business
- Module Paie/RH (CNPS CI)
- Gestion stocks avancée
- Gestion commerciale complète
- Achats & factures fournisseurs
- Immobilisations
- Comptabilité analytique
- Déclaration CNPS

#### Fonctionnalités Techniques
- Connexion bancaire API (banques locales)
- Multi-devises (XOF uniquement MVP)
- Multi-entités (holdings)
- Application mobile native
- Mode offline PWA
- API publique
- Marketplace
- Signature électronique CI

#### Rapports Avancés
- Rapports personnalisés
- Business intelligence
- Forecasting
- Tableaux de bord avancés
- Audit comptable

---

## 👥 5. Équipe & Rôles

### Équipe Minimum Requise
```
1. Product Owner / Business Analyst (full-time)
   - Définition besoins marché CI
   - Tests utilisateurs locaux
   - Documentation français CI

2. Lead Developer Full-Stack (full-time)
   - Architecture
   - Développement backend
   - DevOps
   - Connaissance réglementation CI

3. Frontend Developer (full-time)
   - Interfaces utilisateur
   - UX/UI adapté marché CI
   - Responsive design

4. Expert-comptable/Conseiller fiscal (part-time)
   - Validation conformité CI
   - Plan comptable OHADA CI
   - Formations utilisateurs

5. DevOps / SysAdmin (part-time 50%)
   - Infrastructure locale CI
   - Monitoring
   - Sécurité
```

### Budgeting Équipe
```
Mois 1-4 (Développement):
- Lead Dev: 4 mois
- Frontend: 4 mois
- Expert-comptable: 2 mois
- DevOps: 2 mois équivalent plein

Mois 5+ (Post-MVP):
- Maintenance: 20h/semaine
- Support local: 30h/semaine
- Développement features: variable
```

---

## 📊 6. Métriques de Succès

### Techniques
```
- Performance:
  • Temps chargement page: < 2s (connexion locale CI)
  • Génération PDF: < 3s
  • API response: < 200ms

- Fiabilité:
  • Uptime: > 99.5%
  • Backup réussis: 100%
  • Restauration: < 30min

- Sécurité:
  • Vulnérabilités critiques: 0
  • Tests pénétration: Pass
  • Conformité données locales: 100%
  • Archivage 10 ans: Opérationnel
```

### Business
```
- Acquisition:
  • Inscriptions/semaine: 15+
  • Taux conversion: > 30%
  • CAC: < 30 000 XOF

- Rétention:
  • Churn mensuel: < 8%
  • Utilisation quotidienne: > 70%
  • NPS: > 50

- Monétisation:
  • MRR mois 3: > 500 000 XOF
  • LTV/CAC: > 3.5
  • Paiements échoués: < 3%
  • Taux renouvellement abonnement: > 85%
```

---

## 🚨 7. Gestion des Risques

### Risques Techniques
| Risque | Impact | Probabilité | Mitigation |
|--------|---------|-------------|------------|
| Performance PDF | Haut | Moyenne | Cache + queue + fallback |
| Intégration CinetPay CI | Haut | Haute | Tests sandbox + support local CinetPay |
| Conformité DGI CI | Critique | Moyenne | Validation expert-comptable ivoirien |
| Hébergement local CI | Moyen | Moyenne | Contrat SLA avec hébergeur certifié |
| Archivage 10 ans | Haut | Basse | Système backup automatique + test restore |

### Risques Business
| Risque | Impact | Probabilité | Mitigation |
|--------|---------|-------------|------------|
| Adoption faible PME | Haut | Moyenne | Partenariats chambres commerce CI |
| Concurrence solutions locales | Moyen | Haute | Focus simplicité + conformité |
| Support demandé élevé | Moyen | Haute | Base connaissance complète + chatbot |
| Paiements mobile money échecs | Moyen | Haute | Support dédié + tutoriels vidéo |
| Changement réglementation | Haut | Basse | Veille réglementaire + architecture modulaire |

---

## 📈 8. Roadmap Post-MVP

### Phase 2 (Mois 4-6)
- Module déclaration CNPS
- Application mobile Flutter
- Module achats fournisseurs
- Gestion stocks avancée
- Rapports personnalisés
- Intégration banques locales

### Phase 3 (Mois 7-12)
- Module paie/RH complet
- Connexion API banques
- Signature électronique CI
- Marketplace intégrations
- Intelligence artificielle (prédictions cashflow)

### Phase 4 (Année 2)
- Multi-entités (holdings)
- Comptabilité analytique
- Certification DGI CI
- Module immobilisations
- Expansion Afrique de l'Ouest

---

## 📋 9. Checklist Lancement

### Pré-lancement (Semaine 15)
- [ ] Tests charge avec connexion locale
- [ ] Audit sécurité par expert local
- [ ] Documentation français CI terminée
- [ ] Formation équipe support
- [ ] Onboarding beta-testeurs PME ivoiriennes
- [ ] Validation expert-comptable CI
- [ ] Certificat hébergement données CI

### Lancement Jour J
- [ ] Migration données test → production locale
- [ ] Monitoring actif 24/7
- [ ] Support équipe en alerte
- [ ] Communication lancement (réseaux locaux)
- [ ] Analytics tracking
- [ ] Backup initial

### Post-lancement (30 premiers jours)
- [ ] Revue feedback quotidienne
- [ ] Corrections bugs prioritaires
- [ ] Optimisation performance locale
- [ ] Collecte métriques marché CI
- [ ] Planning Phase 2 basé sur feedback
- [ ] Formation webinars utilisateurs

---

## 📞 10. Support & Maintenance

### Niveaux de Support
```
Niveau 1: Chatbot + Base connaissance (auto-résolution)
Niveau 2: Support chat/email (< 2h réponse heures ouvrables)
Niveau 3: Support téléphone local (+225)
Niveau 4: Support sur site (Abidjan et grandes villes)
```

### SLA (Service Level Agreement)
```
Disponibilité: 99.5% mensuel
Support réponse: < 2h heures ouvrables (8h-18h GMT)
Correction bugs critiques: < 12h
Backup: Quotidien + test hebdomadaire
Maintenance: Fenêtre dimanche 00h-04h
```

### Support Local
```
Adresse physique: Abidjan (à définir)
Téléphone: +225 XX XX XX XX
WhatsApp Business: +225 XX XX XX XX
Email: support@erpci.ci
Horaires: Lundi-Vendredi 8h-18h, Samedi 9h-13h
```

---

## 🎉 Conclusion

Ce plan de 16 semaines permet de livrer un MVP fonctionnel spécifiquement adapté au marché ivoirien, avec accent sur la **facturation conforme DGI CI** et la **déclaration TVA automatisée**, répondant aux besoins critiques des PMEs locales.

**Prochaines étapes immédiates :**
1. Validation réglementaire avec expert-comptable CI
2. Choix hébergeur local certifié
3. Recrutement équipe technique locale
4. Validation besoins avec PME ivoiriennes pilotes

**Success Criteria Principal :** 200 PMEs ivoiriennes actives payantes après 6 mois.

---

*Document version: 2.0*
*Dernière mise à jour: 25 décembre 2024*
*Propriétaire: Product Owner*
*Statut: Adapté marché Côte d'Ivoire*

---

## 📎 Annexes

### A. Glossaire CI
- **OHADA** : Organisation pour l'Harmonisation du Droit des Affaires en Afrique
- **DGI CI** : Direction Générale des Impôts de Côte d'Ivoire
- **IFU** : Identifiant Fiscal Unique (Côte d'Ivoire)
- **NIF** : Numéro d'Identification Fiscale
- **RCCM** : Registre du Commerce et du Crédit Mobilier
- **ICE** : Identifiant Commun de l'Entreprise
- **CNPS** : Caisse Nationale de Prévoyance Sociale
- **TVA** : Taxe sur la Valeur Ajoutée (18% standard, 10% réduit, 0%)
- **RGCI** : Règlementation Générale de la Comptabilité Ivoirienne
- **XOF** : Franc CFA (devise)

### B. Références Spécifiques CI
- [Code Général des Impôts CI](https://www.impots.gouv.ci/)
- [Normes facturation CI](https://www.dgi.gouv.ci/)
- [Plan comptable OHADA CI](https://www.ohada.org)
- [Règlementation CNPS](https://www.cnps.ci/)
- [CinetPay CI](https://cinetpay.com/ci)

### C. Contacts Locaux
- **Responsable technique** : [À définir]
- **Responsable produit** : [À définir]
- **Expert-comptable partenaire** : [À définir]
- **Support** : support@erpci.ci
- **Téléphone** : +225 XX XX XX XX
- **Adresse** : Abidjan, Plateau (à préciser)

### D. Calendrier Fiscal CI
```
Déclaration TVA: 20 du mois suivant
Paiement TVA: 20 du mois suivant
Déclaration CNPS: 15 du mois suivant
Clôture exercice: 31 décembre (général)
Jours fériés: Intégrer calendrier officiel CI
```

---

*Fin du document - Adapté pour la Côte d'Ivoire*

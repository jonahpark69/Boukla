# 💛 Boukla — Site e‑commerce

> Boutique beauté/cheveux : vitrine + catalogue + panier/checkout léger. Projet front (HTML/CSS/JS) avec possibilité d'intégration PHP.

---

## 📑 Sommaire

* [Aperçu](#-aperçu)
* [Fonctionnalités](#-fonctionnalités)
* [Identité & styles](#-identité--styles)
* [Stack & prérequis](#-stack--prérequis)
* [Démarrage](#-démarrage)
* [Arborescence](#-arborescence)
* [Notes techniques](#-notes-techniques)
* [Accessibilité & SEO](#-accessibilité--seo)
* [Captures](#-captures)
* [Roadmap](#-roadmap)
* [Licence](#-licence)

---

## 👀 Aperçu

**Boukla** est un site e‑commerce **léger** présentant une sélection de produits (soin, coiffure). Il inclut un **catalogue** (grille produits), des **pages produit**, un **panier en modale** et un flux de **checkout** simplifié.

> **Demo** : à renseigner
> **Statut** : `WIP`

---

## ✨ Fonctionnalités

* 🛍️ **Catalogue** en grille (tri/filtre basiques selon besoin)
* 📄 **Fiches produit** : visuels, prix, description, CTA *Ajouter au panier*
* 🧺 **Panier en modale** (ou drawer) : ajouter/retirer, total, CTA *Procéder au paiement*
* 💳 **Checkout** : page dédiée (résumé + actions)
* 📬 **Pages** : Accueil, À propos, Contact
* 📱 **Responsive** (desktop → mobile)

---

## 🖌️ Identité & styles

* **Couleurs** : Jaune Boukla `#F2C94C` (primaire) + gris/charcoal pour le texte
* **Typographies** : *Playfair Display* (titres) + *Inter* (texte)
* **Composants UI** : header sticky, hero, grille produits, cartes, modales, boutons CTA

---

## 🧱 Stack & prérequis

* **Front** : HTML5, CSS3, JavaScript (vanilla)
* **Option PHP** : serveur intégré pour le routage simple
* **Outils** (facultatif) : VS Code + Live Server / ou `php -S`

---

## 🚀 Démarrage

### Option A — Front only

```bash
# Ouvrir un serveur statique local (au choix)
# Python
python3 -m http.server 5173
# OU Node
npx serve .
# puis http://localhost:5173
```

### Option B — PHP (si pages .php)

```bash
# À la racine
php -S localhost:8000 -t public
# puis http://localhost:8000
```

> Si Apache/Nginx : pointer le *vhost* vers le dossier `public/`.

---

## 🌲 Arborescence

```
boukla/
├─ public/
│  ├─ assets/
│  │  ├─ css/style.css
│  │  ├─ js/main.js
│  │  └─ images/
│  ├─ index.html            # Home
│  ├─ shop.html             # Grille produits
│  ├─ product.html          # Fiche produit
│  ├─ contact.html          # Formulaire contact
│  ├─ about.html            # À propos
│  └─ checkout.html         # Procéder au paiement
├─ .editorconfig
├─ .gitignore
└─ README.md
```

> Si tu utilises PHP : `index.php`, `shop.php`, etc., même arborescence.

---

## 🛠️ Notes techniques

* **Panier/Checkout** : modale/drawer gérée en JS (ajout/retrait, total, persistance locale optionnelle via `localStorage`).
* **Grille produits** : CSS Grid (3→2→1 colonnes selon largeur), images adaptées (WebP si possible).
* **Styles** : valeurs simples, pas de `clamp()` si non souhaité ; focus visibles.
* **Performances** : lazy‑loading des images, sprites/ICONS SVG.

---

## ♿ Accessibilité & SEO

* HTML sémantique : `header / main / section / nav / footer`
* Titres hiérarchisés (`h1 → h2 → h3`), `alt` descriptifs
* Contrastes suffisants, états focus/hover actifs
* Balises meta de base (title/description, OpenGraph si partage)

---

## 📸 Captures

*(À ajouter après finalisation des pages)*

* Home (hero + highlights)
* Shop (grille)
* Panier (modale)
* Checkout

---

## 🗺️ Roadmap

* [ ] Filtres/tri dans la grille Shop
* [ ] Validation du formulaire Contact (front + messages)
* [ ] Persistance du panier (localStorage)
* [ ] Animation micro‑interactions (hover/boutons)
* [ ] Optimisation images & Lighthouse

---

## 📜 Licence

Projet **privé** (usage personnel/portfolio).


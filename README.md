<p align="center">
  <img width="452" alt="Wellness Connect Logo" src="https://github.com/user-attachments/assets/d61ed0dc-07ff-477d-acfd-060616a6a601" />
</p>

# 🌿 SosI Connect - Écosystème Bien-être

Wellness Connect est une solution complète de suivi du bien-être personnel. Ce projet démontre l'intégration entre une application de bureau performante et une infrastructure web robuste.

## 📌 Architecture du Projet

L'écosystème se divise en deux parties distinctes qui communiquent via une **API REST** :

1. **Web (Symfony) :** Gère la logique métier, la base de données centralisée et fournit une interface d'administration ainsi qu'un client web.
2. **Client Desktop (JavaFX) :** Une application fluide et interactive pour l'utilisateur final, permettant un suivi quotidien sans passer par un navigateur.

[Image of a REST API architecture diagram connecting a web server and a desktop client]

---

## 🛠️ Stack Technique

### Backend / API
* **Framework :** Symfony 6.x / 7.x
* **Langage :** PHP 8.2+
* **Base de données :** MySQL
* **Authentification :** JWT (LexikJWTAuthenticationBundle)

### Desktop
* **Langage :** Java 17+
* **Interface :** JavaFX (avec SceneBuilder pour le FXML)
* **Gestionnaire de dépendances :** Maven / Gradle
* **Client HTTP :** Java HttpClient

---

## ✨ Fonctionnalités

- [x] **Authentification unifiée :** Connexion sécurisée sur les deux plateformes.
- [ ] **Tableau de bord :** Visualisation des indicateurs de santé et de bien-être.


---

## 🚀 Installation

### 1. Configuration du Backend (Symfony)
```bash
cd HealthCareWebDesktopApplication
# dans le terminal taper 
npm install -D tailwindcss@3 postcss autoprefixer
npx tailwindcss -i ./assets/app.css -o ./public/build/tailwind.css --watch
symfony server:start 

# 🚀 CVBuilder Pro - Déploiement InfinityFree (FTP)

## Prérequis
- Compte InfinityFree.com créé
- Client FTP (FileZilla, WinSCP, ou autre)
- Credentials FTP (reçus par email)
- Accès à cPanel pour gérer la database

---

## Step 1: Préparer les fichiers localement

### 1.1 Compiler le build Vue/Vite
```bash
npm run build
```
Cela crée `public/build/` avec tous les assets compilés.

### 1.2 Vérifier APP_KEY
Dans `.env`, note ta clé (ou génère-la si manquante):
```bash
php artisan key:generate
```

Copie la valeur `APP_KEY=base64:...` - tu en auras besoin.

---

## Step 2: Préparer les fichiers pour upload

Tu n'as PAS besoin d'uploader:
- ❌ `node_modules/` (lourd + inutile après build)
- ❌ `vendor/` (sera créé avec composer on-server)
- ❌ `storage/logs/*` (crée automatiquement)

### 2.1 Fichiers à uploader:
```
public/                    (avec build/ compilé)
resources/                 (views, css, js sources)
app/                       (code Laravel)
bootstrap/                 (framework)
config/                    (configuration)
database/                  (migrations, factories, seeders)
routes/                    (web.php, etc)
.env.production            (tu vas adapter)
artisan
composer.json
composer.lock
```

---

## Step 3: Configurer sur InfinityFree

### 3.1 Créer la base de données

1. Accède à **cPanel** (lien dans email InfinityFree)
2. Cherche **MySQL Databases**
3. Crée une nouvelle DB:
   - Database name: `infinityfree_db_name` (avec ton nom de compte en préfixe)
   - Username: `infinityfree_user`
   - Password: Génère un fort (note-le!)
4. Clique **Create Database**

### 3.2 Trouver tes credentials FTP

Dans cPanel → **FTP Account** ou email de bienvenue:
- **Host**: ftp.yourdomain.com
- **Username**: user@yourdomain.com
- **Password**: (fourni)
- **Port**: 21

### 3.3 Dossier public

InfinityFree recompile usually le dossier `public_html/`:
- Tout le code Laravel va dans `public_html/laravel/` 
- Ou utilise **Addon Domain** pour point vers le bon dossier

---

## Step 4: Upload via FTP

### 4.1 Se connecter
Ouvre **FileZilla** ou WinSCP:
1. Host: `ftp.yourdomain.com`
2. Username: `user@yourdomain.com`
3. Password: (ton password FTP)
4. Port: 21
5. Click **Connect**

### 4.2 Uploader les fichiers
```
Local (ton PC)          →    Server (InfinityFree)
app/                    →    public_html/app/
bootstrap/              →    public_html/bootstrap/
config/                 →    public_html/config/
database/               →    public_html/database/
public/                 →    public_html/public/
resources/              →    public_html/resources/
routes/                 →    public_html/routes/
artisan                 →    public_html/artisan
composer.json           →    public_html/composer.json
composer.lock           →    public_html/composer.lock
setup.php               →    public_html/setup.php
.env.production         →    public_html/.env (ou .env.production)
```

⚠️ **Important**: 
- Upload `setup.php` AUSSI
- Crée manuellement les dossiers vides si besoin:
  - `storage/`
  - `storage/app/`
  - `storage/framework/`
  - `storage/logs/`
  - `bootstrap/cache/`

### 4.3 Permissions
Certains dossiers doivent être writable (chmod 755 ou 775):
- `storage/`
- `bootstrap/cache/`
- `public/`

Via FileZilla: Right-click → **File Permissions** → 755

---

## Step 5: Configurer .env sur le serveur

### 5.1 Créer/modifier `.env`
Via cPanel → **File Manager**:
1. Navigate to `public_html/`
2. Click **+ File** → Name: `.env`
3. Edit avec FTP ou cPanel editor:

```
APP_NAME=CVBuilder Pro
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_FROM_LOCAL_ENV

APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=infinityfree_db_name
DB_USERNAME=infinityfree_user
DB_PASSWORD=your_db_password_here

CACHE_DRIVER=file
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

OPENROUTER_API_KEY=your_api_key_here

LOG_CHANNEL=stack
```

⚠️ Remplace les valeurs correctes!

---

## Step 6: Run Setup Script

### 6.1 Visite le setup script
1. Ouvre ton navigateur
2. Va à: `https://yourdomain.com/setup.php`
3. Attends que ça finisse (1-2 minutes)
4. Tu dois voir ✅ "SETUP COMPLETED SUCCESSFULLY"

Si erreurs:
- Vérifies les credentials DB
- Vérifies que dossiers `storage/`, `bootstrap/cache/` sont writable
- Checks logs: `https://yourdomain.com/storage/logs/laravel.log`

### 6.2 Supprime setup.php
⚠️ **IMPORTANT**: Via FTP ou cPanel File Manager, **DELETE `setup.php`**

---

## Step 7: Test et Go Live!

1. Visite: `https://yourdomain.com`
2. Tu vois la page d'accueil? ✅ Success!
3. Test login/register
4. Test conversations

Si erreurs 500:
- Vérifies `.env` settings
- Checks `storage/logs/laravel.log`
- Vérifies database connection

---

## Troubleshooting

**"Class not found" errors**
→ Composer n'a pas install
→ Vérifies que `setup.php` s'est exécuté sans erreurs

**Database connection error**
→ Vérifies `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` dans `.env`
→ Crée une nouvelle user DB si besoin

**Files not found (404)**
→ Assure-toi que `public/build/` est uploadé avec tous les assets
→ Vérifies permissions sur `public/`

**Storage permission denied**
→ Chmod `storage/` à 755 ou 775 via FTP

**"APP_KEY not set"**
→ Set `APP_KEY` dans `.env` (copie de ton `.env` local)

**Queue/Mail issues on InfinityFree**
→ Normal. Assure-toi que:
  - `QUEUE_CONNECTION=sync` (pas "database")
  - `MAIL_DRIVER=log` (ou fourni par InfinityFree)

---

## Vérification Finale

Checklist avant de soumettre ton exam:
- [ ] Site est accessible via HTTPS
- [ ] Login/Register fonctionne
- [ ] Base de données connectée (conversations sauvegardées)
- [ ] CSS/JS chargés correctement (pas d'erreurs 404)
- [ ] `setup.php` supprimé
- [ ] `.env` ne fait pas leak de secrets

---

## URL finale pour l'exam

Fournis cette URL pour AA 2.2 (Déploiement):
```
https://yourdomain.com
```

✅ Déploiement complete!

# Git Workflow dan Branching Strategy

## Struktur Branch

Repository ini menggunakan **Git Flow** sebagai strategi branching dengan struktur sebagai berikut:

### Branch Utama

- **`main`** - Branch produksi yang berisi kode stabil dan siap rilis
- **`develop`** - Branch pengembangan utama untuk integrasi fitur-fitur baru

### Branch Pendukung

#### Feature Branches
```bash
feature/<nama_fitur>
```
**Contoh:**
- `feature/user-authentication`
- `feature/url-analytics`
- `feature/dashboard-ui`

**Cara penggunaan:**
```bash
# Membuat feature branch dari develop
git checkout develop
git checkout -b feature/nama-fitur

# Setelah selesai development
git checkout develop
git merge feature/nama-fitur
git branch -d feature/nama-fitur
```

#### Release Branches
```bash
release/<versi>
```
**Contoh:**
- `release/1.1.0`
- `release/2.0.0`

**Cara penggunaan:**
```bash
# Membuat release branch dari develop
git checkout develop
git checkout -b release/1.1.0

# Setelah testing dan bug fixes
git checkout main
git merge release/1.1.0
git tag -a v1.1.0 -m "Release v1.1.0"
git checkout develop
git merge release/1.1.0
git branch -d release/1.1.0
```

#### Hotfix Branches
```bash
hotfix/<nama_perbaikan>
```
**Contoh:**
- `hotfix/security-patch`
- `hotfix/critical-bug-fix`

**Cara penggunaan:**
```bash
# Membuat hotfix branch dari main
git checkout main
git checkout -b hotfix/nama-perbaikan

# Setelah perbaikan
git checkout main
git merge hotfix/nama-perbaikan
git tag -a v1.0.1 -m "Hotfix v1.0.1"
git checkout develop
git merge hotfix/nama-perbaikan
git branch -d hotfix/nama-perbaikan
```

## Semantic Versioning (SemVer)

Proyek ini menggunakan **Semantic Versioning** dengan format:

```
MAJOR.MINOR.PATCH
```

### Aturan Versioning

- **MAJOR** (X.0.0) - Perubahan yang tidak kompatibel dengan versi sebelumnya
- **MINOR** (0.X.0) - Penambahan fitur baru yang kompatibel dengan versi sebelumnya
- **PATCH** (0.0.X) - Perbaikan bug yang kompatibel dengan versi sebelumnya

### Contoh Versioning

- `1.0.0` - Rilis pertama (current)
- `1.0.1` - Perbaikan bug kecil
- `1.1.0` - Penambahan fitur baru
- `2.0.0` - Perubahan besar yang tidak kompatibel

### Pre-release Versions

Untuk versi pre-release, gunakan format:
- `1.1.0-alpha.1` - Versi alpha
- `1.1.0-beta.1` - Versi beta
- `1.1.0-rc.1` - Release candidate

## Commit Message Convention

Gunakan format commit message yang konsisten:

```
type(scope): description

[optional body]

[optional footer]
```

### Types
- `feat` - Fitur baru
- `fix` - Perbaikan bug
- `docs` - Perubahan dokumentasi
- `style` - Perubahan formatting, tidak mengubah logika
- `refactor` - Refactoring kode
- `test` - Menambah atau mengubah test
- `chore` - Perubahan build process atau auxiliary tools

### Contoh Commit Messages
```bash
feat(auth): add user registration functionality
fix(url): resolve short URL generation bug
docs(readme): update installation instructions
test(analytics): add unit tests for click tracking
```

## Tag dan Release

### Membuat Tag
```bash
# Annotated tag untuk release
git tag -a v1.0.0 -m "Release v1.0.0 - Initial stable release"

# Lightweight tag untuk milestone
git tag v1.0.0-beta.1
```

### Melihat Tag
```bash
# Melihat semua tag
git tag

# Melihat tag dengan pesan
git tag -n

# Melihat detail tag
git show v1.0.0
```

## Best Practices

1. **Selalu buat feature branch** dari `develop` untuk fitur baru
2. **Gunakan pull request** untuk review kode sebelum merge
3. **Tulis commit message yang jelas** dan deskriptif
4. **Test kode** sebelum merge ke `develop`
5. **Update version number** sesuai dengan perubahan yang dibuat
6. **Buat tag** untuk setiap release
7. **Hapus branch** yang sudah tidak digunakan

## Status Repository Saat Ini

- **Current Version:** v1.0.0
- **Main Branch:** `main` (production-ready)
- **Development Branch:** `develop` (active development)
- **Latest Tag:** v1.0.0 - Initial stable release

## Fitur yang Sudah Tersedia (v1.0.0)

- ✅ Laravel 11 dengan Breeze Vue starter kit
- ✅ Sistem URL shortening
- ✅ Analytics tracking
- ✅ User management dengan roles
- ✅ Modern UI dengan TailwindCSS
- ✅ Comprehensive test suite
- ✅ Git workflow setup

---

**Catatan:** Dokumentasi ini akan diupdate seiring dengan perkembangan proyek.
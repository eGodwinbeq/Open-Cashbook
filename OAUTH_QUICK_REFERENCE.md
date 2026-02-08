# Quick Reference: Google OAuth Authentication

## 🚀 Quick Start

### 1. Environment Setup
Add to `.env`:
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 2. Get Credentials
1. Visit: https://console.cloud.google.com/
2. Create OAuth 2.0 Client ID
3. Add redirect URI: `http://localhost:8000/auth/google/callback`
4. Copy Client ID & Secret to `.env`

### 3. Test
```bash
php artisan serve
# Visit: http://localhost:8000/login
# Click: "Continue with Google"
```

## 📋 Key Files

| File | Purpose |
|------|---------|
| `OAuthController.php` | Handles Google OAuth flow |
| `routes/auth.php` | OAuth routes defined here |
| `login.blade.php` | Google button is primary |
| `register.blade.php` | Google button is primary |
| `config/services.php` | Google config |

## 🔑 Routes

```php
GET  /auth/google           → Redirect to Google
GET  /auth/google/callback  → Handle callback
```

## 💾 Database Fields

```
users.google_id  → Unique Google ID
users.avatar     → Profile picture URL
users.password   → Nullable for OAuth users
```

## 🎨 UI Layout

```
┌─────────────────────────────────┐
│     "Continue with Google"      │ ← PRIMARY (large, prominent)
├─────────────────────────────────┤
│  Or continue with email         │ ← Divider
├─────────────────────────────────┤
│   Email/Password Form           │ ← SECONDARY (below divider)
└─────────────────────────────────┘
```

## 🔐 Security

- ✅ Auto-verified emails for Google users
- ✅ Random secure passwords for OAuth users
- ✅ Account linking by email
- ✅ Remember me enabled

## 🐛 Troubleshooting

**Error: redirect_uri_mismatch**
→ Check Google Console redirect URI matches exactly

**Error: Client ID not found**
→ Verify `.env` has correct credentials with no quotes/spaces

**Can't find Socialite**
→ Run: `composer require laravel/socialite`

## 📚 Documentation

- Full setup: `GOOGLE_OAUTH_SETUP.md`
- Implementation details: `GOOGLE_OAUTH_IMPLEMENTATION.md`
- Laravel Socialite: https://laravel.com/docs/socialite

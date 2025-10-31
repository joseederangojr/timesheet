# cPanel Deployment Guide

This guide explains how to deploy the Laravel Timesheet application to cPanel using GitHub Actions.

## Prerequisites

1. **cPanel Account** with SSH access enabled
2. **GitHub Repository** with the application code
3. **Domain/Subdomain** pointing to your cPanel account
4. **SSH Key Pair** for secure authentication

## GitHub Secrets Setup

You need to add the following secrets to your GitHub repository:

1. Go to **Settings** → **Secrets and variables** → **Actions**
2. Add these secrets:

### Required Secrets

| Secret Name              | Description                     | Example                                  |
| ------------------------ | ------------------------------- | ---------------------------------------- |
| `CPANEL_SSH_HOST`        | Your cPanel SSH hostname        | `yourdomain.com` or server IP            |
| `CPANEL_SSH_USERNAME`    | SSH username                    | `youruser` (cPanel username)             |
| `CPANEL_SSH_PRIVATE_KEY` | Private SSH key                 | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `CPANEL_SSH_PORT`        | SSH port (optional)             | `22` (default)                           |
| `CPANEL_DEPLOY_PATH`     | Deployment directory (optional) | `/home/youruser/public_html`             |

### Setting Up SSH Access in cPanel

1. **Login to cPanel**
2. **Go to "Security" → "SSH Access"**
3. **Click "Manage SSH Keys"**
4. **Generate a new SSH key pair** or import your existing public key
5. **Authorize the key** for your cPanel user
6. **Copy the private key** and add it to GitHub secrets

### Finding Your cPanel SSH Details

1. **SSH Host**: Usually your domain name or the server's IP address
2. **SSH Username**: Your cPanel username (without @domain.com)
3. **SSH Port**: Usually 22 (default), but check with your hosting provider
4. **Deploy Path**: `/home/YOUR_USERNAME/public_html` (replace YOUR_USERNAME with your cPanel username)

## Environment Configuration

### Production .env File

The deployment script automatically handles the `.env` file:

1. **First Deployment**: Uses `.env.example` as a template
2. **Subsequent Deployments**: Preserves existing `.env` from the `shared` directory

**Important settings to configure in production:**

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://yourdomain.com`
- Database credentials (if using database)
- Mail configuration
- Any other production-specific settings

### Setting Up Production Environment

**Option 1: Manual Setup**

1. After first deployment, SSH into your server
2. Edit the `.env` file in the shared directory: `nano /home/youruser/public_html/shared/.env`
3. Configure your production settings

**Option 2: Pre-configure**

1. Create a production `.env` file locally
2. Upload it to `shared/.env` on your server before deployment

The deployment script will automatically use the existing `.env` file from the shared directory for subsequent deployments.

## Deployment Workflow

### Automatic Deployment

The deployment runs automatically when you push to the `main` branch, after tests pass. The SSH action handles:

- ✅ Cloning the latest code
- ✅ Installing dependencies
- ✅ Building assets
- ✅ Setting proper permissions
- ✅ Optimizing Laravel for production
- ✅ Creating storage symlinks
- ✅ Zero-downtime deployment with releases

### Manual Deployment

You can also trigger deployment manually:

1. Go to **Actions** tab in GitHub
2. Select **"deploy"** workflow
3. Click **"Run workflow"**
4. Optionally check **"Force deployment even if tests fail"**

## Post-Deployment Tasks

The SSH deployment script handles most server-side tasks automatically. However, you may need to:

### Database Migrations

If you need to run database migrations:

```bash
# SSH into your server
ssh youruser@yourdomain.com

# Navigate to current release
cd /home/youruser/public_html/current

# Run migrations
php artisan migrate --force
```

### Manual Environment Updates

If you need to update environment variables after deployment:

```bash
# Edit the shared .env file
nano /home/youruser/public_html/shared/.env

# Clear and recache configuration
cd /home/youruser/public_html/current
php artisan config:cache
```

## Troubleshooting

### Common Issues

1. **SSH Connection Failed**
    - Verify SSH key is properly added to GitHub secrets
    - Check that SSH access is enabled in cPanel
    - Confirm the SSH host, username, and port are correct

2. **"Permission denied" errors**
    - The deployment script sets permissions automatically
    - If issues persist, check cPanel user permissions

3. **"Class not found" errors**
    - Usually resolved automatically by the deployment script
    - If persistent, check the current symlink: `ls -la ~/public_html/current`

4. **"Route not found" errors**
    - Clear route cache: `cd ~/public_html/current && php artisan route:clear && php artisan route:cache`

5. **Assets not loading**
    - Check that `npm run build` completed: `ls -la ~/public_html/current/public/build/`
    - Verify the build files exist and have correct permissions

6. **Database connection issues**
    - Check `.env` in shared directory: `cat ~/public_html/shared/.env`
    - Verify database credentials and host accessibility

7. **Deployment stuck/frozen**
    - Check GitHub Actions logs for timeout or error messages
    - Verify SSH connection works: `ssh -T youruser@yourdomain.com`

### Checking Logs

```bash
# View Laravel logs (from current release)
tail -f ~/public_html/shared/storage/logs/laravel.log

# Check deployment logs (if available)
tail -f ~/public_html/deployment.log

# Check cPanel error logs
tail -f ~/logs/error_log

# Check Apache/PHP error logs
tail -f ~/logs/access_log
```

### Rollback Procedures

```bash
# SSH into your server
ssh youruser@yourdomain.com

# List available releases
ls -la ~/public_html/releases/

# Switch to previous release
ln -sfn ~/public_html/releases/20241201_140000 ~/public_html/current

# Clear caches
cd ~/public_html/current
php artisan config:clear
php artisan cache:clear
```

## File Structure After Deployment

The SSH deployment creates a structured deployment with releases for zero-downtime updates:

```
public_html/
├── current -> releases/20241201_143022/  # Symlink to current release
├── releases/
│   ├── 20241201_143022/                  # Release directory (date/time)
│   ├── 20241201_142015/                  # Previous release
│   └── ...                               # Older releases (auto-cleaned)
├── shared/
│   ├── .env                              # Shared environment file
│   ├── storage/                          # Shared storage directory
│   │   ├── app/
│   │   ├── framework/
│   │   └── logs/
│   └── bootstrap/
│       └── cache/                        # Shared cache directory
└── .htaccess                             # Web server config
```

Each release contains:

```
releases/20241201_143022/
├── app/
├── bootstrap/ -> ../shared/bootstrap/     # Symlinked
├── config/
├── database/
├── public/
│   ├── build/                            # Compiled assets
│   ├── index.php                         # Laravel entry point
│   └── storage -> ../shared/storage/      # Symlinked
├── resources/
├── routes/
├── storage -> ../shared/storage/          # Symlinked
├── vendor/
├── .env -> ../shared/.env                 # Symlinked
├── artisan
└── composer.json
```

## Security Considerations

1. **Never commit `.env` files** to version control
2. **Use SSH keys** instead of passwords for authentication
3. **Keep dependencies updated** for security patches
4. **Monitor logs regularly** for suspicious activity
5. **Use HTTPS** in production (configure SSL in cPanel)
6. **Restrict SSH access** to specific IP addresses if possible
7. **Regularly rotate** SSH keys and database passwords

## Rollback

If you need to rollback:

1. Go to GitHub Actions
2. Find the previous successful deployment
3. Download the deployment artifacts
4. Upload them manually via FTP

## Manual Deployment (Alternative)

If you prefer manual deployment or need to deploy from your local machine:

1. Upload `deploy.sh` to your server
2. Make it executable: `chmod +x deploy.sh`
3. Run the script: `./deploy.sh`
4. For migrations: `./deploy.sh --migrate`

## Support

If you encounter issues:

1. Check the deployment logs in GitHub Actions
2. Review cPanel error logs
3. Verify SSH key and server details are correct
4. Test the SSH connection manually: `ssh -T youruser@yourdomain.com`
5. Check the deployment structure: `ls -la ~/public_html/`
6. Use the manual deployment script for troubleshooting

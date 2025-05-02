
# 📁 File Share API

A simple Laravel-based file sharing API (inspired by WeTransfer) that allows users to upload files, share them via a unique link, and optionally secure them with a password and email notifications.

## 🚀 Features

- Upload single or multiple files
- Get a unique shareable download link
- Optional password protection for downloads
- Optional email notifications with the download link
- Files auto-delete after a set expiration period (default: 7 days)
- RESTful API responses (JSON)

---

## 🛠 Tech Stack

- **Backend:** Laravel 10+
- **Database:** MySQL / SQLite / PostgreSQL
- **Storage:** Local or any Laravel-supported storage (S3, etc.)
- **Mail:** Laravel mail driver (SMTP, Mailgun, etc.)

---

## 📦 Installation

### Prerequisites

- PHP 8.1+
- Composer
- MySQL or other supported DB
- Laravel CLI (optional)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/your-username/file-share-api.git
cd file-share-api

# 2. Install dependencies
composer install

# 3. Copy .env and configure
cp .env.example .env

# 4. Set app key
php artisan key:generate

# 5. Configure .env (DB, mail, etc.)
# Fill in your DB credentials, mail settings, etc.

# 6. Run migrations
php artisan migrate

# 7. Serve the app
php artisan serve
```

---

## 📤 API Endpoints

### Upload Files

**POST** `/api/upload`

**Form Data:**
- `files[]`: File(s) to upload (required)
- `email_to`: Recipient email (optional)
- `email_from`: Sender email (optional)
- `message`: Message body (optional)
- `password`: Optional password to protect download

**Response:**
```json
{
  "download_url": "http://yourdomain.com/api/download/abcd1234"
}
```

---

### Download File

**GET** `/api/download/{uuid}`

**Query Parameters (if needed):**
- `password`: Required if file is password-protected

**Response:**
- Downloads the file

---

## 🔐 Security

- All files are stored with a UUID to prevent predictable access.
- Optional password protection uses hashed storage (bcrypt).
- Email notifications only sent if both `email_to` and `email_from` are provided.

---

## 🧹 Cleanup

To automatically delete expired files, add this to your **Scheduler** (`App\Console\Kernel`):

```php
$schedule->command('files:cleanup')->daily();
```

Run the artisan command manually:

```bash
php artisan files:cleanup
```

---

## ✉️ Email Configuration

In `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="FileShare"
```

Use [Mailtrap](https://mailtrap.io/) or a real SMTP service for testing.

---

## 🧪 Testing (optional)

You can write feature tests for:
- File uploads
- Protected downloads
- Expired files
- Email notifications

---

## 📄 License

MIT License

---

## 🙌 Acknowledgements

- Inspired by [WeTransfer](https://wetransfer.com/)
- Built with [Laravel](https://laravel.com/)

# Laravel 12 – OTP Based Auth API Demo

A clean, scalable **OTP-based Authentication API** built with **Laravel 12** and **Sanctum**, designed for **API-first applications** such as mobile apps, SPA frontends, and third-party integrations.

This project is structured in a **production-ready** way and can be easily converted into a **reusable Composer package**.



## 🚀 Features

- ✅ API-only authentication (Blade used only for email templates)
- ✅ Email OTP based registration & verification
- ✅ OTP resend with cooldown
- ✅ Login only after email verification
- ✅ Forgot password with OTP verification
- ✅ Password reset & change password
- ✅ Sanctum token authentication
- ✅ Clean controller separation (Auth, OTP, Password, Profile)
- ✅ Centralized API response format
- ✅ Centralized HTTP status codes & messages
- ✅ Service-based OTP handling
- ✅ Ready for Postman / Mobile apps



## 🧱 Tech Stack

- **Laravel:** 12.x  
- **PHP:** 8.2+  
- **Auth:** Laravel Sanctum  
- **Mail:** SMTP (Gmail supported)  
- **Database:** MySQL (or any Laravel-supported DB)



## 📂 Project Structure (Auth Related)

```
app/
├── Constants/
│   └── StatusCode.php
│
├── Helpers/
│   └── ApiResponse.php
│
├── Services/
│   └── OtpService.php
│
├── Http/Controllers/Api/Auth/
│   ├── RegisterController.php
│   ├── LoginController.php
│   ├── OtpController.php
│   ├── PasswordController.php
│   └── ProfileController.php
│
├── Mail/
│   └── SendOtpMail.php
│
resources/
└── views/
    └── emails/
        └── otp.blade.php
```



## ⚙️ Installation & Setup

### 1️⃣ Clone Project

```bash
git clone https://github.com/Rutvik-Bhingradiya/Laravel-auth-demo
cd laravel-auth-demo
```

### 2️⃣ Install Dependencies
```bash
composer install
```

### 3️⃣ Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```
Update database credentials in .env.

### 4️⃣ Run Migrations
```bash
php artisan migrate
```

## ✉️ Mail Configuration (Gmail SMTP)

#### ⚠️ Gmail requires App Password, not your normal password.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password_without_spaces
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="Auth Demo"
```

After updating .env:
```bash
php artisan optimize:clear
```

## 🔐 API Routes (v1)
Base URL:
```bash
/api/v1
```
## 📌 API Routes

### 🔓 Public Routes (No Authentication Required)

| Method | Endpoint | Description |
|------|---------|------------|
| POST | `/register` | Register user & send OTP |
| POST | `/verify-otp` | Verify email OTP |
| POST | `/resend-otp` | Resend OTP |
| POST | `/login` | Login (verified users only) |
| POST | `/forgot-password` | Send forgot password OTP |
| POST | `/verify-forgot-otp` | Verify forgot password OTP |
| POST | `/reset-password` | Reset password |

---

### 🔐 Protected Routes (Sanctum Authentication)

> **Authorization:** Bearer Token required

| Method | Endpoint | Description |
|------|---------|------------|
| GET | `/profile` | Get logged-in user |
| POST | `/change-password` | Change password |
| POST | `/logout` | Logout user |

## 📮 Postman Collection

This project includes a ready-to-use **Postman collection** for testing all authentication APIs.

### 📥 Import Collection

1. Open **Postman**
2. Click **Import**
3. Select file: postman/Laravel-Auth-API.postman_collection.json
4. Set environment variable: base_url = http://127.0.0.1:8000/api/v1


## 🔑 Authorization

All **protected routes** require an access token.

Include the token in the request header as shown below:



## 📦 API Response Format

All API responses follow a **standard and consistent structure**.

### ✅ Success / Error Response Example

```json
{
  "status": true,
  "message": "Success message",
  "data": {},
  "errors": null
}
```

## 🔍 Response Fields Description

| Field | Type | Description |
|------|------|------------|
| status | Boolean | Indicates success or failure |
| message | String | Response message |
| data | Object | Returned data (if any) |
| errors | Object / Null | Validation or error details |


## 🧪 Testing

You can test all APIs using **Postman** or any other API client.

### ✅ Recommended Practices

- Use an **environment variable** for the base URL
- **Auto-save token** after successful login
- Test the **complete OTP flow** from registration to password reset



## 🧠 OTP Flow Summary

1. User registers → OTP sent to email  
2. User verifies OTP → Email verified  
3. User logs in → Sanctum access token generated  
4. Forgot password → OTP sent to email  
5. Verify OTP → User allowed to reset password  


## 🔒 Security Notes

- OTP expiry time is configurable
- OTP resend cooldown is implemented
- Passwords are hashed using **bcrypt**
- Tokens are revoked on password reset
- Email verification is required before login



## 🔄 Future Enhancements

- ⏳ Queue email sending
- 🔐 Hash OTP in database
- 🚦 Rate limiting on OTP APIs
- 🧪 Feature tests
- 📦 Convert to Composer package
- 📄 OpenAPI / Swagger documentation (optional)



## 📜 License

This project is open-source and available under the **MIT License**.



## 🤝 Contribution

Feel free to **fork**, **improve**, or **reuse** this structure in your own projects.



## ✨ Author

Built with ❤️ using **Laravel 12**  
Designed for **reusability** & **scalability**


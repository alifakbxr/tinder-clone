# Tinder Clone - Dating App

A modern dating application built with Laravel backend and React Native frontend, featuring user swiping, matching, and location-based recommendations.

## ✨ Key Features

- **User Authentication**: Secure login/signup with Laravel Sanctum
- **Profile Management**: Create profiles with multiple pictures and location data
- **Card Swiping**: Smooth like/nope gestures using react-native-deck-swiper
- **Smart Recommendations**: Location-based user discovery algorithm
- **Match System**: Mutual like detection and notifications
- **Liked Users List**: View and manage your liked profiles
- **Popular User Notifications**: Automated admin alerts for users with 50+ likes

## 🛠 Technology Stack

**Backend**: Laravel 10, MySQL, Laravel Sanctum, Swagger API docs
**Frontend**: React Native, Expo, TypeScript, React Query, Recoil

## 📋 Prerequisites

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+
- Git

## 🚀 Quick Setup

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
# Edit .env with your database credentials
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
npx expo start
```

## 🎯 Usage

1. **Start Backend**: `php artisan serve` (runs on http://localhost:8000)
2. **Start Frontend**: `npx expo start` (scan QR code with Expo Go app)
3. **Register/Login**: Create account and start swiping!
4. **API Docs**: Visit http://localhost:8000/api/documentation

## 📱 How It Works

- **Swipe Right**: Like a user
- **Swipe Left**: Pass on a user
- **Matches**: When both users like each other
- **Recommendations**: Smart algorithm shows relevant users
- **Profile Pictures**: Upload multiple photos in your profile

## 🔧 API Endpoints

- `POST /api/login` - User authentication
- `GET /api/users/recommendations` - Get user recommendations
- `POST /api/swipes` - Create swipe action (like/nope)
- `GET /api/users/liked` - Get your liked users

## 📄 License

MIT License
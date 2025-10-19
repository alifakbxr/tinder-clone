# Tinder Clone - Dating App

A modern, full-stack dating application built with Laravel and React Native, featuring real-time swiping, user matching, and location-based recommendations.

## 🚀 Features

### Core Functionality
- **User Authentication**: Secure login/signup with Laravel Sanctum
- **Profile Management**: Create and manage user profiles with multiple pictures
- **Card Swiping**: Smooth swipe gestures for like/nope actions using react-native-deck-swiper
- **Smart Recommendations**: Location-based user discovery with intelligent algorithm
- **Match System**: Get notified when mutual likes occur
- **Liked Users List**: View and manage your liked profiles

### Advanced Features
- **Popular User Notifications**: Automated system to notify admins when users reach 50+ likes
- **Optimistic Updates**: Instant UI feedback with React Query for seamless user experience
- **Infinite Scrolling**: Efficient pagination for browsing recommendations
- **Location-Based Discovery**: Find users near you with latitude/longitude coordinates
- **API Documentation**: Comprehensive Swagger/OpenAPI documentation

### Technical Features
- **Real-time Updates**: Live data synchronization between frontend and backend
- **Responsive Design**: Mobile-first approach with React Native/Expo
- **State Management**: Efficient state handling with Recoil
- **Error Handling**: Robust error handling and retry mechanisms
- **Caching Strategy**: Smart caching with React Query for optimal performance

## 🛠 Technology Stack

### Backend
- **Laravel 10**: PHP web framework
- **MySQL**: Relational database
- **Laravel Sanctum**: API authentication
- **Laravel Swagger**: API documentation
- **Laravel Vite**: Asset building

### Frontend
- **React Native**: Cross-platform mobile framework
- **Expo**: Development platform and build tools
- **TypeScript**: Type-safe JavaScript
- **React Query (TanStack Query)**: Data fetching and caching
- **Recoil**: State management
- **React Navigation**: Navigation library
- **Axios**: HTTP client

### Development Tools
- **React Native Deck Swiper**: Card swiping component
- **React Native Reanimated**: Smooth animations
- **React Native Gesture Handler**: Touch gestures
- **Expo Linear Gradient**: UI gradients
- **Expo Vector Icons**: Icon library

## 📋 Prerequisites

Before running this application, ensure you have the following installed:

- **PHP 8.1 or higher**
- **Composer** (PHP dependency manager)
- **Node.js 16+** and **npm**
- **MySQL 8.0+**
- **Git**
- **Expo CLI** (`npm install -g @expo/cli`)

## 🚀 Installation

### Backend Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tinder-clone/backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tinder_clone
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

6. **Install Node dependencies for asset building**
   ```bash
   npm install
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

### Frontend Setup

1. **Navigate to frontend directory**
   ```bash
   cd ../frontend
   ```

2. **Install Node dependencies**
   ```bash
   npm install
   ```

3. **Start the development server**
   ```bash
   npx expo start
   ```

## 🔧 Configuration

### Backend Configuration

1. **Mail Configuration** (for popular user notifications)
   Update `.env` file:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email@domain.com
   MAIL_PASSWORD=your-password
   MAIL_FROM_ADDRESS=admin@tinder-clone.com
   ```

2. **API Configuration**
   The frontend connects to `http://localhost:8000/api` by default. Update in `frontend/src/services/api.js` if needed.

### Frontend Configuration

1. **API Base URL**
   Update in `frontend/src/services/api.js`:
   ```javascript
   const api = axios.create({
     baseURL: 'http://your-backend-url/api',
   });
   ```

## 🎯 Usage

### Starting the Application

1. **Start the Laravel backend**
   ```bash
   cd backend
   php artisan serve
   ```
   Backend will be available at `http://localhost:8000`

2. **Start the React Native frontend**
   ```bash
   cd frontend
   npx expo start
   ```

3. **Access the app**
   - Scan QR code with Expo Go app (iOS/Android)
   - Press `i` for iOS simulator
   - Press `a` for Android emulator
   - Press `w` for web browser

### API Endpoints

#### Authentication
- `POST /api/login` - User login
- `GET /api/user` - Get authenticated user info

#### User Recommendations
- `GET /api/users/recommendations` - Get user recommendations (paginated)

#### Swipe Actions
- `POST /api/swipes` - Create a swipe action (like/nope)

#### Liked Users
- `GET /api/users/liked` - Get users you've liked (paginated)

### User Registration

1. **Create User Account**
   - Use the app's registration flow or create users via database seeder
   - Users must have: name, email, password, age, latitude, longitude

2. **Add Profile Pictures**
   - Upload pictures through the app interface
   - Pictures are stored with order for display sequence

### Swiping Mechanics

1. **Browse Recommendations**
   - Swipe right to like a user
   - Swipe left to pass (nope)
   - Optimistic updates provide instant feedback

2. **View Liked Users**
   - Access your liked users list
   - Infinite scroll through paginated results

## 📊 Database Schema

### Users Table
- `id` - Primary key
- `name` - User's display name
- `email` - Unique email address
- `password` - Hashed password
- `age` - User's age
- `latitude` - Location latitude
- `longitude` - Location longitude
- `popular_notification_sent_at` - Timestamp for popular user notification

### User Pictures Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `url` - Picture URL/path
- `order` - Display order

### Swipes Table
- `id` - Primary key
- `swiper_id` - User performing the swipe
- `swiped_id` - User being swiped on
- `action` - 'like' or 'nope'

## 🔄 Automated Tasks

### Popular User Notifications

The application includes a scheduled command to notify administrators when users become popular:

```bash
php artisan app:check-popular-users
```

This command:
- Identifies users with 50+ likes who haven't been notified
- Sends email notifications to administrators
- Updates the notification timestamp to prevent duplicate notifications

## 🧪 Development

### Running Tests

```bash
# Backend tests
cd backend
php artisan test

# Frontend linting
cd ../frontend
npm run lint
```

### Code Structure

```
backend/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # API controllers
│   ├── Http/Resources/       # API resources
│   ├── Mail/                 # Email templates
│   └── Models/               # Eloquent models
├── database/migrations/      # Database migrations
└── routes/api.php           # API routes

frontend/
├── src/
│   ├── components/          # Reusable UI components
│   ├── hooks/               # Custom React hooks
│   ├── screens/             # App screens
│   ├── services/            # API services
│   └── state/               # State management
```

## 🚀 Deployment

### Backend Deployment

1. **Environment Setup**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. **Database Migration**
   ```bash
   php artisan migrate --force
   ```

3. **Cache Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Frontend Deployment

1. **Build for Production**
   ```bash
   npx expo build:android  # or ios
   ```

2. **Update API URLs**
   - Update production API endpoints in `frontend/src/services/api.js`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 API Documentation

API documentation is available via Swagger UI when the Laravel backend is running:
- Visit: `http://localhost:8000/api/documentation`

## 🔐 Security Features

- **CSRF Protection**: Laravel's built-in CSRF protection
- **API Authentication**: Laravel Sanctum token-based authentication
- **Password Hashing**: Secure password storage with bcrypt
- **Rate Limiting**: Configurable API rate limiting
- **Input Validation**: Comprehensive request validation

## 📞 Support

For support and questions:
- Create an issue in the repository
- Contact the development team

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Note**: This is a demo application. In production, implement proper security measures, error monitoring, and user data protection compliance.
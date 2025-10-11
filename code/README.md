# 🎓 PFE Management System (Système de Gestion PFE)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [User Roles](#user-roles)
- [API Documentation](#api-documentation)
- [Multilingual Support](#multilingual-support)
- [Contributing](#contributing)
- [License](#license)

## 🎯 About

The **PFE Management System** (Projet de Fin d'Études / Final Year Project Management System) is a comprehensive web application designed to streamline the management of final year projects in academic institutions. Built with Laravel 12, this system provides a complete solution for managing students, teachers, projects, defenses, and academic workflows.

### 🎨 Key Objectives

- **Streamline Project Management**: Simplify the process of proposing, validating, and assigning final year projects
- **Automated Workflow**: Reduce manual administrative tasks through intelligent automation
- **Multi-role Support**: Provide tailored experiences for students, teachers, department heads, and administrators
- **Real-time Tracking**: Monitor project progress, deadlines, and defense schedules
- **Multilingual Interface**: Support for English, French, and Arabic languages

## ✨ Features

### 🎓 **Student Features**
- Browse and select available subjects
- Form teams with other students
- Submit project proposals (internal/external)
- Track project progress and deadlines
- Submit project deliverables
- View defense schedules and grades

### 👨‍🏫 **Teacher Features**
- Propose and manage subjects
- Supervise student projects
- Grade student submissions
- Participate in defense juries
- Generate progress reports

### 🏢 **Department Head Features**
- Validate proposed subjects
- Schedule defense sessions
- Manage room allocations
- Resolve scheduling conflicts
- Oversee department activities

### ⚙️ **Administrator Features**
- Comprehensive system management
- User account management
- Academic year configuration
- Speciality management
- System analytics and reports
- Backup and maintenance tools

### 🔧 **Core System Features**
- **Subject Management**: Internal and external project proposals
- **Team Formation**: Flexible team creation and management
- **Allocation System**: Intelligent subject assignment algorithms
- **Defense Scheduling**: Automated conflict-free scheduling
- **Grade Management**: Comprehensive grading and verification system
- **Conflict Resolution**: Automated detection and resolution tools
- **Analytics Dashboard**: Real-time insights and statistics
- **Notification System**: Email and in-app notifications
- **Document Management**: File uploads and document generation

## 🛠 Technology Stack

### **Backend**
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **PDF Generation**: DomPDF
- **Excel Processing**: Maatwebsite Excel
- **Permissions**: Spatie Laravel Permission

### **Frontend**
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + Alpine.js
- **Icons**: Bootstrap Icons + Font Awesome
- **Charts**: Chart.js
- **UI Components**: Custom dashboard components

### **Development Tools**
- **Code Quality**: Laravel Pint
- **Testing**: PHPUnit
- **Debugging**: Laravel Debugbar
- **Asset Building**: Vite
- **Database**: Laravel Migrations & Seeders

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-username/pfe-management-system.git
cd pfe-management-system
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pfe_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Or run specific seeders
php artisan db:seed --class=AlgerianTestDataSeeder
```

### Step 6: Build Assets

```bash
# Development build
npm run dev

# Production build
npm run build
```

### Step 7: Start the Application

```bash
# Start development server
php artisan serve

# The application will be available at http://localhost:8000
```

## ⚙️ Configuration

### Academic Year Setup

```bash
# Create a new academic year
php artisan academic-year:create "2024-2025"

# Set current academic year
php artisan academic-year:activate "2024-2025"
```

### User Roles Configuration

The system includes four main roles:

1. **Student** (`student`)
2. **Teacher** (`teacher`)
3. **Department Head** (`department_head`)
4. **Administrator** (`admin`)

### System Settings

Access the admin panel to configure:

- University information
- Academic calendar
- Defense scheduling rules
- Email notifications
- File upload limits

## 🗄️ Database Setup

### Core Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts and profiles |
| `specialities` | Academic specialities |
| `subjects` | Project subjects/topics |
| `teams` | Student teams |
| `projects` | Active projects |
| `defenses` | Defense sessions |
| `allocations` | Subject assignments |
| `grades` | Student grades |

### Sample Data

The system includes comprehensive test data:

- 10 Algerian teachers with diverse specialties
- 10 students across different academic levels
- 10 project subjects covering various domains
- 5 complete teams with assigned projects
- Defense rooms and scheduling data

## 💻 Usage

### Default Login Credentials

After seeding, you can use these test accounts:

**Administrator:**
- Email: `admin@univ.dz`
- Password: `password123`

**Teacher:**
- Email: `ahmed.benali@univ.dz`
- Password: `password123`

**Student:**
- Email: `amine.boubekeur@etudiant.univ.dz`
- Password: `password123`

### Quick Start Guide

1. **Login** with appropriate credentials
2. **Dashboard** provides role-specific overview
3. **Navigation** through the sidebar menu
4. **Language** switching via header dropdown
5. **Help** tooltips and guided tours available

## 👥 User Roles

### 🎓 Student Workflow

1. **Team Formation**: Join or create a team
2. **Subject Selection**: Browse and select preferred subjects
3. **Project Work**: Submit deliverables and track progress
4. **Defense**: Participate in project defense

### 👨‍🏫 Teacher Workflow

1. **Subject Proposal**: Create and submit project subjects
2. **Student Supervision**: Guide assigned teams
3. **Grading**: Evaluate student submissions
4. **Defense Participation**: Serve on defense juries

### 🏢 Department Head Workflow

1. **Subject Validation**: Approve proposed subjects
2. **Schedule Management**: Plan defense sessions
3. **Conflict Resolution**: Handle scheduling conflicts
4. **Oversight**: Monitor department activities

### ⚙️ Administrator Workflow

1. **System Configuration**: Manage global settings
2. **User Management**: Create and manage accounts
3. **Analytics**: Monitor system usage and performance
4. **Maintenance**: Backup and system health monitoring

## 🌐 Multilingual Support

The system supports three languages:

- **English** (`en`) - Default
- **French** (`fr`) - Système français
- **Arabic** (`ar`) - النظام العربي

### Language Files

- `resources/lang/en/app.php`
- `resources/lang/fr/app.php`
- `resources/lang/ar/app.php`

### Adding New Languages

1. Create new language file: `resources/lang/{locale}/app.php`
2. Add locale to `config/app.php`
3. Update language selector in views

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Generate coverage report
php artisan test --coverage
```

## 📊 System Analytics

### Available Reports

- **User Statistics**: Registration trends and activity
- **Subject Analysis**: Proposal and allocation metrics
- **Defense Metrics**: Scheduling and completion rates
- **Grade Distribution**: Academic performance analytics

### Dashboard Features

- Real-time system health monitoring
- Quick statistics and KPIs
- Interactive charts and graphs
- Export capabilities (PDF/Excel)

## 🔧 Maintenance

### Regular Tasks

```bash
# Clear application cache
php artisan cache:clear

# Optimize for production
php artisan optimize

# Database maintenance
php artisan migrate:status
php artisan db:show

# Log rotation
php artisan log:clear
```

### Backup Commands

```bash
# Create system backup
php artisan backup:create

# Restore from backup
php artisan backup:restore {backup-file}
```

## 🤝 Contributing

We welcome contributions to improve the PFE Management System!

### Development Guidelines

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Commit** your changes: `git commit -m 'Add amazing feature'`
4. **Push** to the branch: `git push origin feature/amazing-feature`
5. **Submit** a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for API changes
- Use meaningful commit messages

### Reporting Issues

Please use the GitHub Issues tab to report:

- 🐛 Bugs and errors
- 💡 Feature requests
- 📚 Documentation improvements
- 🔧 Performance issues

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Framework** - Excellent foundation for rapid development
- **Bootstrap** - Responsive UI components
- **Font Awesome** - Beautiful icons
- **Chart.js** - Interactive charts and graphs
- **Algerian Universities** - Requirements and feedback

## 📞 Support

For support and questions:

- 📧 Email: support@pfe-management.com
- 📱 GitHub Issues: [Report an Issue](https://github.com/your-username/pfe-management-system/issues)
- 📖 Documentation: [Wiki](https://github.com/your-username/pfe-management-system/wiki)

## 🗺️ Roadmap

### Upcoming Features

- [ ] Mobile application (React Native)
- [ ] API v2 with enhanced endpoints
- [ ] Advanced analytics dashboard
- [ ] Integration with LMS systems
- [ ] Automated plagiarism detection
- [ ] Video conferencing integration
- [ ] Enhanced notification system
- [ ] Multi-tenant support

---

<p align="center">
  <strong>Built with ❤️ for Academic Excellence</strong>
</p>

<p align="center">
  <sub>© 2024 PFE Management System. All rights reserved.</sub>
</p>
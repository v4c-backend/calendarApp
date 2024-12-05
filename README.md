# Calendar App 📅
Description
A simple and intuitive Calendar Application built to manage events. This app is designed to offer user-friendly navigation
![image alt](![image](https://github.com/user-attachments/assets/120db99d-f74b-4cc3-9074-e487e308aabf)
)
# Features
📆 Event Management: Create, update, and delete events.
🕒 Time Management: View schedules by day, week, or month.
📚 Data Persistence: Events are saved and synced across sessions.

# Tech Stack
Backend: Laravel v10
Frontend: JavaScript calendar plugin
Database: MySQL 

# Installation
https://github.com/v4c-backend/calendarApp.git
cd calendar-app
composer install
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=calendar_db
DB_USERNAME=root
DB_PASSWORD=

php artisan migrate
php artisan serve

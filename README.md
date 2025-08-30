# Note Taking App

## Overview
A simple PHP-MySQL note-taking application with user authentication, profile management, and customizable UI (dark mode, Notion-style theme).

## Features
- User login/logout
- Profile management:
  - Update username, full name, email, bio
  - Upload/remove profile picture with zoom preview
- Add, edit, delete notes
- Category and search filters
- Dark mode toggle with icon update
- Notion-style theme for cleaner UI

## Database
- **users**: id, username, full_name, email, password, bio, profile_pic, created_at  
- **notes**: id, user_id, title, content, category, created_at, updated_at  

## Setup
1. Clone the repository.
2. Import `database.sql` to MySQL.
3. Update `db.php` with your database credentials.
4. Run the project on a local server (XAMPP, MAMP, etc).

## File Structure
- `/uploads/` – stores user profile pictures  
- `/css/` – custom styles (`style.css` for Notion theme)  
- `db.php` – database connection  
- `profile.php` – user profile page  
- `update_profile.php` – handles profile updates  
- `index.php` / `dashboard.php` – main note dashboard  

## Notes
- Use `.gitignore` to exclude `/uploads` and sensitive files.
- Tailwind CDN is used for styling.
- Make sure `uploads` folder is writable.

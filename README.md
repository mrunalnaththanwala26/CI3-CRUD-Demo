# CodeIgniter 3 + MySQL CRUD (uploads, hobbies, mail)

## Features
- Create / Read / Update / Delete users
- Search + pagination
- Profile picture upload (jpg/png/gif, 2MB, random filename, old file removed on replace/delete)
- Hobbies (checkboxes, many-to-many via `user_hobbies`)
- Email: welcome mail on user creation + "Send Mail" form per user (SMTP)
- Server-side validation, unique email check, delete via POST, XSS-escaped output

## Setup
1. Copy this folder to your web root (XAMPP: `htdocs/ci3_crud`, needs PHP 7.x/8.0 - CI 3.1.13 supports up to 8.1 with deprecation notices).
2. Import `database.sql` (phpMyAdmin or `mysql -u root < database.sql`).
3. Edit `application/config/database.php` if your MySQL user/password differ.
4. Edit `application/config/config.php` -> `base_url` if your URL differs.
5. Edit `application/config/email.php` -> SMTP user + app password.
6. Make sure `uploads/profiles` and `application/cache` and `application/logs` are writable.
7. Enable Apache `mod_rewrite` (`AllowOverride All`) for clean URLs, otherwise set
   `$config['index_page'] = 'index.php';`
8. Open http://localhost/ci3_crud/

## Structure
- application/controllers/Users.php   - all actions
- application/models/User_model.php   - DB logic
- application/views/users/*           - list, form, view, mail
- application/config/email.php        - SMTP settings
- database.sql                        - schema + sample hobbies

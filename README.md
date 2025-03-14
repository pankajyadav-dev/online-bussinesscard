# Business Card Creator

A web application built with PHP, HTML, JavaScript, and Tailwind CSS that allows users to create, manage, and share digital business cards.

## Features

- User registration with email verification via OTP
- Login and logout functionality
- Browse different business card designs by category
- Create custom business cards with your information
- Edit and delete your business cards
- Share business cards via email
- Generate QR code for your business card
- View public shared business cards
- Modern and responsive design using Tailwind CSS

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB database
- Composer (for managing PHP dependencies)
- Web server (e.g., Apache or Nginx)

## Installation

1. Clone the repository:
```
git clone https://github.com/pankajyadav-dev/online-bussinesscard.git
cd business-card-creator
```

2. Install PHP dependencies using Composer:
```
composer require phpmailer/phpmailer
```

3. Create a MySQL database and import the schema:
```
mysql -u username -p your_database_name < database/schema.sql
```

4. Configure the database connection:
   Edit `config/database.php` and update the database connection details:
   ```php
   $host = "localhost";     // Your database host
   $dbname = "business_card_db";  // Your database name
   $username = "root";      // Your database username
   $password = "";          // Your database password
   ```

5. Configure email settings:
   Edit `includes/functions.php` and update the SMTP settings in the `sendEmail()` function:
   ```php
   $mail->Host       = 'smtp.gmail.com';          // SMTP server
   $mail->Username   = 'your-email@gmail.com';    // SMTP username
   $mail->Password   = 'your-password';           // SMTP password
   $mail->setFrom('your-email@gmail.com', 'Business Card Creator');
   ```

6. Set up the web server:
   - For Apache, ensure that the document root points to the project directory
   - For Nginx, configure the server block to point to the project directory

## Project Structure

```
business-card-creator/
├── assets/                  # Images and other static assets
├── config/                  # Configuration files
│   └── database.php         # Database connection
├── css/                     # Stylesheets
│   └── style.css            # Custom CSS
├── database/                # Database scripts
│   └── schema.sql           # Database schema
├── includes/                # Reusable components
│   ├── footer.php           # Footer include
│   ├── functions.php        # Common functions
│   └── header.php           # Header include
├── js/                      # JavaScript files
│   └── main.js              # Main JavaScript
├── pages/                   # Application pages
│   ├── auth/                # Authentication pages
│   │   ├── login.php        # Login page
│   │   ├── logout.php       # Logout script
│   │   ├── register.php     # Registration page
│   │   └── verify.php       # Email verification page
│   ├── cards/               # Business card pages
│   │   ├── create.php       # Create card page
│   │   ├── delete.php       # Delete card page
│   │   ├── designs.php      # Card designs page
│   │   ├── edit.php         # Edit card page
│   │   ├── share.php        # Share card page
│   │   └── view.php         # View card page
│   └── profile/             # User profile pages
│       ├── account.php      # Account settings page
│       └── dashboard.php    # User dashboard page
├── index.php                # Home page
└── README.md                # This file
```

## Usage

1. Open your browser and navigate to the project URL
2. Register a new account using your email
3. Verify your email using the OTP sent to your inbox
4. Browse card designs and create your business card
5. Share your business card via email or QR code

## Security Considerations

- Update the PHP mailer with your secure email credentials
- Keep your database credentials secure
- Consider implementing HTTPS for secure connections
- Regularly update dependencies to patch security vulnerabilities

## License

This project is open-source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. 
📚 BurauenBiblio Installation Guide (with Sample Data)
✅ Prerequisites

    Install WAMP
    Download and install WAMP with PHP version 7.4.26 or 8.0.13.

    Set MySQL Root Password
    During installation or afterward via phpMyAdmin, configure the MySQL root password.

📦 Extract Files

Unzip the provided OpenBiblio package into the following directory:

C:\wamp64\www\

🗃️ Prepare the Sample SQL File

Copy the sample file from the sql folder:

burauenbib_sample_complete.sql

Paste it into:

C:\wamp64\tmp\

🛠️ Import the Sample Database

    Open Command Prompt

    Log in to MySQL:

mysql -u root -p

Enter the following commands:

    CREATE DATABASE IF NOT EXISTS burauenbib;
    USE burauenbib;
    SOURCE C:/wamp64/tmp/burauenbib_sample_complete.sql;

👤 Create a Database User

Still inside the MySQL prompt:

CREATE USER 'burauenuser'@'localhost' IDENTIFIED BY 'burauenpassword';
GRANT ALL PRIVILEGES ON `burauenbib`.* TO 'burauenuser'@'localhost';

⚙️ Configure Database Credentials

Edit the file:

C:\wamp64\www\your_folder_name\shared\dbParams.php

Make sure it contains the correct values:

<?php
$this->dsn["dbEngine"] = 'mysql';
$this->dsn["host"] = 'localhost';
$this->dsn["username"] = 'burauenuser';
$this->dsn["pwd"] = 'burauenpassword';
$this->dsn["database"] = 'burauenbib';
$this->dsn["mode"] = 'haveConst';
?>

    ⚠️ Note: You may change the username and password for better security. Just make sure they match in both MySQL and dbParams.php.

🚀 Start WAMP & Access the Site

    Launch WAMP.

    Open your browser and go to:

    http://localhost/

🔐 Login to BurauenBiblio

Use the following credentials:

Username: admin
Password: admin

    ⚠️ Important: For security reasons, change the default login credentials immediately via the Admin menu.
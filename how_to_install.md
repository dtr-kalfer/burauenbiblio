# 📚 BurauenBiblio Installation Guide (with Sample Data) in Windows 10 Setup

## ✅ Prerequisites

Install WAMP

Download and install WAMP with PHP version 7.4.26 or 8.0.13.

Set MySQL Root Password

During installation or afterward via phpMyAdmin, configure the MySQL root password.

## 📦 Extract Files

Unzip the downloaded burauenbiblio zip package directly into the following directory:

C:\wamp64\www\

## 🗃️ Prepare the Sample SQL File

Copy the sample file from the sql folder:

    burauenbib_sample_clean.sql

Paste it into:

    C:\wamp64\tmp\

## 🛠️ Import the Sample Database

Open Command Prompt (click start menu, click run, enter on prompt: cmd)

Log in to MySQL (enter using this command)

*Please double check your mysql version on the same path, it may be different from mine, adjust the path accordingly.*

    cd C:\wamp64\bin\mysql\mysql5.7.36\bin\

    mysql -u root -p

(Enter your mysql root password when prompted)

Enter the following commands:

    CREATE DATABASE IF NOT EXISTS burauenbib;
    USE burauenbib;
    SOURCE C:/wamp64/tmp/burauenbib_sample_clean.sql

## 👤 Create a Database User

Still inside the MySQL prompt:

    CREATE USER 'burauenuser'@'localhost' IDENTIFIED BY 'burauenpassword';
    GRANT ALL PRIVILEGES ON `burauenbib`.* TO 'burauenuser'@'localhost';

## ⚙️ Configure Database Credentials

Edit the file:

    C:\wamp64\www\dbParams.php

Make sure it contains the correct values:

    <?php
    $this->dsn["dbEngine"] = 'mysql';
    $this->dsn["host"] = 'localhost';
    $this->dsn["username"] = 'burauenuser';
    $this->dsn["pwd"] = 'burauenpassword';
    $this->dsn["database"] = 'burauenbib';
    $this->dsn["mode"] = 'haveConst';
    ?>

⚠️ Note: You may **change the username and password** for security. Just make sure they **match** in both MySQL prompt you have entered and dbParams.php.

## 🚀 Start WAMP & Access the Site

    Launch WAMP.

    Open your browser and go to:

    http://localhost/

## 🔐 Login to BurauenBiblio

    Use the following credentials:

    Username: admin
    Password: admin

⚠️ Important: For security reasons, **change the default login credentials immediately** via the Admin menu.


### 🔐 Updating from earlier Burauenbiblio
Note: If you are updating from earlier Burauenbiblio, please make a backup copy of your original dbParams.php. Extract the Burauenbiblio.zip, overwrite the dbParams.php using your backup copy.
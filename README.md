# RoboPlay Scoreboard 2
Web Based Score Keeping System for the [UC Davis C-STEM Center](http://c-stem.ucdavis.edu)'s [RoboPlay Challenge Competition](http://c-stem.ucdavis.edu/roboplay/challenge/) and [RoboPlay Video Competition](http://c-stem.ucdavis.edu/roboplay/video/).

Updated to Laravel Framework 5.5

## Server Side Dependencies
* [Composer](https://getcomposer.org/)
* MySQL
* PHP >= 7.1.0
   * PDO Extension
   * OpenSSL Extension
   * Mbstring Extension
   * Tokenizer Extension
   * XML Extension
* Apache2 or Nginx
   * mod_rewrite
* Redis 
* docx2txt
* pdftotext

## Client Side Dependencies
* Node/npm/Babel

## Environment Dependencies
Invoices are synced from a Formidable Install in a joined Wordpress Database.

## Install
1. Clone Repo Locally
1. Run `composer install` in top level folder 
1. Install Apache/MySQL/PHP
1. Point Apache to `public/` folder
    * Note: Scoreboard 2 assumes `public/` is root.
    * Setting up a "local subdomain" for development is recommended.
1. Setup MySQL Databases and Users
1. Copy `.env.example` to `.env` and enter missing variables
    Reccomended:  
    CACHE_DRIVER=redis  
    SESSION_DRIVER=redis  
    QUEUE_DRIVER=redis      
1. Run `php artisan migrate` to setup DB, or install master DB files. 

## Queue Processing
Notification e-mails are processed by `artisan queue:work`. For development work you can simply leave this running in the background, so long as you have configured an e-mail server (or the default mailtrap.io).

For production, a process monitor such as `Supervisor` is recommended.


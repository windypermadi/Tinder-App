<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400">
  </a>
</p>

<p align="center">
  <strong>Backend (PHP Laravel) - Tinder App API</strong>
</p>

<p align="center">
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.x-blue.svg" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-8-red.svg" alt="Laravel"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-Relational-green.svg" alt="MySQL"></a>
  <a href="https://swagger.io/"><img src="https://img.shields.io/badge/Swagger-API-yellow.svg" alt="Swagger"></a>
</p>

---

## Description

This repository contains the **backend API** for a Tinder-like application, developed as a **technical assignment** using **PHP Laravel 8**.  
The backend handles user management, people recommendations, like/dislike actions, and notifications via cronjob.

---

## People Data

The data model for people includes the following fields:

- **name** – Person's name  
- **age** – Person's age  
- **pictures** – Person's pictures (can be multiple)  
- **location** – Person's location  

---

## Required Features

1. List of recommended people (with pagination)  
2. Like a person  
3. Dislike a person  
4. Liked people list (API only)  
5. Cronjob: if a person receives more than 50 likes, an email is sent to the admin (any email can be used)  

---

## Infrastructure Requirements

1. Must use **PHP Laravel 8**  
2. Create **RDB schema** (database tables and relationships)  
3. Create **Swagger documentation** and deploy it to be testable  

---

## Technologies Used

- **PHP 8.x**  
- **Laravel 8**  
- **MySQL / MariaDB** (Relational Database)  
- **Swagger** (API Documentation)  

---

## Setup Instructions

1. Clone this repository  
   ```bash
   git clone https://github.com/windypermadi/Tinder-App.git

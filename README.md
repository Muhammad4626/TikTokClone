# TikTok Clone Project

A fully functional web-based social media application that replicates core features of TikTok. This project allows users to upload short videos, follow other users, like and comment on content, and exchange messages in real-time.

Built using **PHP**, **MySQL**, **JavaScript**, and **CSS**.

## 🚀 Features

### User Authentication
* **Sign Up & Login:** Secure user registration and authentication system.
* **Session Management:** Protected routes ensuring only logged-in users can access features.

### Video Interaction
* **Video Feed:** Vertical scrolling feed with auto-play functionality similar to the original app.
* **Upload Videos:** Users can upload video files (MP4, WebM, OGG) with titles and descriptions.
* **Like System:** Users can like videos, with real-time counter updates.
* **Comments:** Users can post comments on videos and view discussions.

### Social Networking
* **Follow System:** Ability to follow and unfollow other users.
* **Friend Suggestions:** "Suggested Users" section to discover new people to follow.
* **Profile Management:** View user profiles, follower/following counts, and total likes. Includes an "Edit Profile" feature to update username, email, and profile picture.

### Messaging
* **Inbox:** Dedicated inbox to view conversation history.
* **Direct Messaging:** Send and receive messages with other users.

### Interface
* **Responsive Design:** styled with CSS to mimic the mobile-app experience.
* **Dynamic UI:** JavaScript handles modal popups for comments, uploads, and editing profiles without page reloads.

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **Backend:** PHP (Native)
* **Database:** MySQL
* **Server Environment:** XAMPP (Apache/MySQL)

## ⚙️ Installation & Setup

Follow these steps to run the project locally:

1.  **Prerequisites:**
    * Download and install [XAMPP](https://www.apachefriends.org/index.html) (or any local server environment that supports PHP and MySQL).

2.  **Clone the Repository:**
    * Navigate to the `htdocs` folder in your XAMPP installation directory (usually `C:\xampp\htdocs`).
    * Clone this repository or extract the project files into a folder named `tiktok_project` (or your preferred name).

3.  **Database Configuration:**
    * Open the XAMPP Control Panel and start **Apache** and **MySQL**.
    * Go to your browser and navigate to `http://localhost/phpmyadmin`.
    * Create a new database named `tiktok_clone`.
    * Import the `tiktok_clone.sql` file provided in the project root to create the necessary tables (`users`, `videos`, `likes`, `comments`, `friends`, `messages`).

4.  **Connect to Database:**
    * Ensure `includes/db_connect.php` matches your local database credentials. By default, it is configured for XAMPP:
        ```php
        $host = 'localhost';
        $db = 'tiktok_clone';
        $user = 'root';
        $pass = ''; 
        ```

5.  **Run the App:**
    * Open your browser and type: `http://localhost/tiktok_project/` (replace `tiktok_project` with your folder name).
    * You will be redirected to the Login/Signup page.

## 📂 Project Structure

* `includes/` - Contains database connection and reusable components like the navbar.
* `images/` - Stores user profile pictures.
* `Uploads/videos/` - Stores uploaded video files.
* `*.php` - Core logic for backend operations (auth, uploading, interaction).
* `styles.css` - Global styling for the application.
* `main.js` - Frontend logic for event listeners, AJAX calls, and UI interactivity.

## 👥 Authors

* **Muhammad Muzammil**
* **Muhammad Hasaan Asim**

## 📄 License

This project is created for educational purposes.

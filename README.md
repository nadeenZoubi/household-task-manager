#  Household Task Manager - Web Application

This project is a collaborative **Household Task Management System**, developed as part of Homework 3 for the course **Web-Based Systems Development**. The application allows users to manage and assign household tasks, invite other users to join their household, and interact with tasks dynamically — all with a modern and responsive interface.

The project is built using **PHP**, **MySQL**, **AJAX**, **HTML**, **CSS**, and **Bootstrap**, and is intended to run on a local **XAMPP** server environment.

---

##  Key Features

### User System
- **Registration page** with real-time email availability check via **AJAX**.
- Secure **login system** with session management.
- Restricted access: all pages are protected unless the user is logged in.

###  Household Collaboration
- Invite other registered users to your household.
- **Autocomplete** email input when adding users, based on existing users in the database.
- Modal-based form for inviting participants (no page reload).

###  Dynamic Task Management
- **Add new tasks without refreshing the page**.
- Automatically clear and reset form fields after task submission.
- Assign a **responsible user** from the list of household members.
- Mark tasks as completed with a checkbox — works instantly via AJAX.
- Delete tasks dynamically with immediate feedback.

###  Responsive UI
- Clean and intuitive design using **Bootstrap**.
- Fully responsive for mobile, tablet, and desktop.
- Smooth user experience with modals and dynamic content updates.

---
# How to Run PHP Code

## 1. Using a Local Server (like XAMPP / MAMP / WAMP)

1. **Install XAMPP**  
  (https://www.apachefriends.org/index.html)

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Save your PHP file in the `htdocs` folder**  
   For example:  
   `C:\xampp\htdocs\Household_Task_Manager`

4. **Access the file via the browser:**  
   Example URL:  
   `http://localhost/Household_Task_Manager`

---

##  Project Structure

```bash
/
├── css/               # Custom CSS styles
├── js/                # JavaScript and AJAX scripts
├── includes/          # PHP includes (DB connection, session handlers, etc.)
├── pages/             # Core application pages (login, register, dashboard, etc.)
├── assets/            # Icons, images
├── index.php          # Main entry page
├── db.sql             # MySQL database schema


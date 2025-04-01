# Event management of a cultural association
 
### **Project Explanation: Event Management for "Association FARHA"**

This project is about developing a **web application in PHP** to manage event reservations for the **FARHA association**. It builds on a previously created database ("Association Farha") and aims to provide a platform for users to browse, filter, and book event tickets.

---

## **Main Features of the Web Application:**

### **1. Home Page (index.php)**
- Displays **upcoming events** in a catalog (sorted by date).
- Each event is shown as a **card** with:
  - **Title**
  - **Image**
  - **Date**
  - **Category**
  - **Button "J’achète" (Buy)** if tickets are available.
  - **Button "Guichet fermé" (Sold Out)** if no tickets are left.
- **Search Bar:** Allows users to search for events by **title**.
- **Filters:** 
  - Events between two given **dates**.
  - Events by **category** (Music, Cinema, Theater, etc.).

### **2. Event Details Page**
- Clicking "J’achète" or "Guichet fermé" redirects to this page.
- Displays **detailed information** about the event:
  - **Title**
  - **Large Image**
  - **Description**
  - **Date**
- **Ticket Purchase Form**:
  - Users can select the number of tickets (**normal/reduced price**).
  - **Users must be logged in** to complete the purchase.
- **Purchase Validation:**
  - If the user is logged in, clicking **"Valider"** shows a **confirmation message**.
  - A successful purchase is only possible **if tickets are available**.
  - Seat numbers are assigned **sequentially** based on venue capacity.

### **3. Authentication & Registration**
- New users must **register** before purchasing tickets.
- Existing users must **log in** before making a purchase.

### **4. User Profile Page**
Once logged in, users can:
- **Edit personal information** (name, email, etc.).
- **View purchase history**:
  - A **table** showing: invoice reference, purchase date, total paid.
  - **Buttons:**
    - "Voir mes billets" (View Tickets).
    - "Voir ma facture" (View Invoice).

---

## **Project Deliverables**
To complete the project, you need to submit:
1. **A well-structured project folder** (Code + Documentation).
2. **The database**.
3. **A project management file or link**.

---

## **Skills You Will Develop**
- **Frontend (C2 & C3):** Creating a static & dynamic web interface.
- **Backend (C6 & C7):** Managing database access & backend logic.

---

## **What to Include in the GitHub README?**
Your **README.md** file should have:

### **1. Project Title & Description**
- **Project Name:** Event Management for Association FARHA
- **Short Description:** A PHP-based web application for managing event reservations, ticket purchases, and user profiles.

### **2. Features**
- **Event Listing** (Search & Filter).
- **Event Details & Booking System**.
- **User Authentication & Profile Management**.
- **Ticket & Invoice Management**.

### **3. Technologies Used**
- **Frontend:** HTML, CSS, JavaScript.
- **Backend:** PHP.
- **Database:** MySQL (MariaDB in XAMPP).
- **Version Control:** Git & GitHub.

### **Project Structure and Required Pages (PHP Web Application for Event Management)**

To build this project, you need several **PHP pages** for different functionalities. Here’s an organized **page structure** and what each file should do.

---

## **1. Main Pages**
### **📌 index.php (Home Page)**
- Lists **upcoming events** as **cards**.
- Displays:
  - Event **Title**
  - Event **Image**
  - Event **Date**
  - Event **Category** (Music, Cinema, etc.)
  - **“Buy” (J’achète) button** if tickets are available.
  - **“Sold Out” (Guichet fermé) button** if tickets are sold out.
- **Search Bar**: Users can search events by **title**.
- **Filters**: Users can filter events by **date range** or **category**.

---

### **📌 event.php (Event Details Page)**
- Displays full details of an event when a user clicks on an event in `index.php`.
- Shows:
  - **Large event image**
  - **Title**
  - **Date**
  - **Category**
  - **Description**
- **Ticket Purchase Form:**
  - Input for selecting the number of tickets.
  - Normal price & reduced price.
  - **Only available if the user is logged in**.
  - Clicking "Validate" (`Valider`) checks **ticket availability** and assigns seat numbers.

---

### **📌 login.php (Login Page)**
- Allows users to **log in** with:
  - Email
  - Password
- If login is successful, redirects to **index.php**.
- If the user is **not registered**, provides a **link to register.php**.

---

### **📌 register.php (Registration Page)**
- Allows **new users** to create an account with:
  - First Name
  - Last Name
  - Email
  - Password (hashed in the database)
- After successful registration, redirects to **login.php**.

---

### **📌 profile.php (User Profile)**
- Only accessible if the user is **logged in**.
- Displays user information (name, email, etc.).
- Allows users to **update personal details**.
- Displays a **table of past purchases**, including:
  - Invoice reference
  - Purchase date
  - Total paid
  - **Buttons** to view tickets & invoices.

---

### **📌 purchase.php (Ticket Purchase Processing)**
- Handles ticket purchases when the user clicks **“Validate”** on `event.php`.
- **Checks availability**:
  - If enough tickets are available, assigns **seat numbers**.
  - If sold out, shows an **error message**.
- Saves the **purchase in the database**.
- Redirects to a **confirmation page**.

---

### **📌 confirmation.php (Purchase Confirmation Page)**
- Displays **confirmation message** after successful ticket purchase.
- Shows:
  - Event details
  - Number of tickets bought
  - **Buttons to download invoice & tickets**.

---

### **📌 logout.php**
- Ends the user session and redirects to `index.php`.

---

## **2. Admin Pages (Optional)**
If you need an **admin panel**, create these extra pages:

### **📌 admin/index.php**
- Requires **admin login**.
- Lists all events with **edit & delete options**.

### **📌 admin/add_event.php**
- Form to **add a new event** to the database.

### **📌 admin/edit_event.php**
- Allows admins to **edit an existing event**.

### **📌 admin/delete_event.php**
- Deletes an event from the database.

---

## **3. Database Connection File**
### **📌 db.php**
- **Handles connection** to the MySQL database.
- Used in all PHP files to query the database.

---

## **4. Style & Assets**
- `style.css` (CSS for design)
- `scripts.js` (for frontend validation & interactions)
- `images/` (folder for event images)

---

## **5. Project Folder Structure**
```
/event-management/
│── index.php
│── event.php
│── login.php
│── register.php
│── profile.php
│── purchase.php
│── confirmation.php
│── logout.php
│── db.php
│── style.css
│── scripts.js
│── /images/
│── /admin/
│   ├── index.php
│   ├── add_event.php
│   ├── edit_event.php
│   ├── delete_event.php
│── /uploads/ (For storing invoices/tickets)
```

---

## **Next Steps**
1. **Do you want me to generate a README file?**
2. **Do you need help with database structure (`events` table, `users` table, etc.)?** 🚀

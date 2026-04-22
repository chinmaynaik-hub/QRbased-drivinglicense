# QR code based Driving license system (RTO Management System)

The **RTO Management System** is a web-based application designed to digitalize and secure the driving license management process.  
Our main goal is to reduce data leaks when a person loses their driving license by embedding user information in a **QR code** making it **non-human readable** and viewable only by authorized personnel.

🌐 **Live Demo**: [https://qrdrivinglicense.infinityfree.me](https://qrdrivinglicense.infinityfree.me)

---

##  Project Objective

- To minimize data leaks and fraud in driving license management.
- To store license holder details securely in the form of a **QR code**.
- To allow only authorized users to access or verify the encoded information.
- To streamline the process of applying and approving learning and driving licenses.

---

##  Features

###  User Module
- Apply for **Learning License (LL)**.
- Check **LL application status** (Approved / Pending).
- Apply for **Driving License (DL)** once LL is approved.
- Generate a **QR code** containing all user details (non-human readable).
- **Client-side QR storage** - QR codes stored in browser localStorage (not on server).
- **Download QR code** as PNG image for offline use.

###  Admin Module
- **Admin login** to access the control panel.
- Verify user details and approve/reject license applications.
- Manage user data and license records efficiently.
- View and edit LL/DL applications.

---

##  Security Note

Currently, the QR code data is **not encrypted or decrypted**.  
However, future updates will include **encryption and decryption mechanisms** to enhance data security.

---

##  Technologies Used

| Layer | Technologies |
|-------|---------------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Others | Composer (for QR Code generation) |

---

##  Project Setup

### 1️. Install XAMPP
Download and install **[XAMPP](https://www.apachefriends.org/)** to run Apache and MySQL servers.

### 2️. Add Files
- Locate the `htdocs` folder inside the XAMPP directory.
- Copy all the project files to this folder.

### 3️. Database Setup
- Start **Apache** and **MySQL** from XAMPP Control Panel.
- Open your browser and visit:  
  `http://localhost/phpmyadmin`
- Create a new database named `rto`.
- Import the file `rto.sql` from the **database** folder.

### 4️. QR Code Setup
- Download and install **Composer** from [getcomposer.org](https://getcomposer.org/).
- Add Composer’s installation path to your **system environment variables**.
- The project uses Composer for generating QR codes.

### 5️. Run the Project
- Open your browser and visit:  
  `http://localhost/(your_project_folder_name)`
- The project will now run on your **localhost server**.

### 6️. Hosted Version
- **The project is live at: [https://qrdrivinglicense.infinityfree.me](https://qrdrivinglicense.infinityfree.me)**
- Hosted on **InfinityFree** with client-side QR code storage (no server storage required)

---

##  Future Enhancements

- Implement **encryption & decryption** for QR code data.
- Add **email/SMS notifications** for application status updates.
- Improve **UI/UX** with modern design frameworks.
- Add **role-based access control** for enhanced security.

---

##  Contributors

- Chinmay Naik [Project Developer]
- Team Members: [[Chetan](https://github.com/chetan-mi), [Chinmay soratur](https://github.com/chinmayrs), [bheemanagowda](https://github.com/Bheemangowda2405)]

---

##  License

This project is open-source and available under the **MIT License**.




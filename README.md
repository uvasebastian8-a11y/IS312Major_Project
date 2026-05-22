# IS312Major_Project

# User Manual – Library System with Book Reviews

## 1. Introduction

Welcome to the **Library System with Book Reviews**. This web application allows you to browse books, read reviews, and share your own reading experiences. The system supports three types of users:

- **Guest** – can browse books and read reviews without logging in.
- **Registered Customer** – can write, edit, and delete their own reviews, and manage their profile dashboard.
- **Administrator** – can add new books to the library.

This manual explains how to use all features.

---

## 2. Getting Started

### 2.1 Accessing the Website

Open your web browser and go to the website URL provided by your instructor (or your local server address, e.g., `http://localhost/library_system/`).

You will see the **Home Page** with a list of books and a navigation bar.

### 2.2 Navigation Bar

The navigation bar changes based on whether you are logged in:

| Link | Visible to | Action |
|------|------------|--------|
| Home | Everyone | Returns to the book listing page |
| Reviews | Everyone | Shows all user reviews, with filter by book |
| Login | Guests only | Takes you to the login form |
| Register | Guests only | Takes you to the registration form |
| Dashboard | Logged-in customers | Shows your profile and your reviews |
| Add Book | Admin only | Allows admin to add a new book |
| Logout | Logged-in users | Ends your session and returns to Home |

---

## 3. Guest Features (No Login Required)

### 3.1 Browse Books

- On the **Home Page**, you see all books in the library.
- Click a book cover or title to go to its **Book Details** page.

### 3.2 Read Reviews

- On the **Book Details** page, you can read all reviews submitted by registered users.
- You will also see the **average rating** and the number of reviews.

### 3.3 View All Reviews

- Click **Reviews** in the navigation bar.
- You can filter reviews by selecting a book from the dropdown menu.

> **Note:** Guests cannot write reviews. You must register and log in to submit a review.

---

## 4. Registration

To become a registered customer:

1. Click **Register** in the navigation bar.
2. Fill in the form:
   - **First Name** and **Last Name** (required)
   - **Gender** (optional)
   - **Address**, **Postal Code**, **P.O. Number** (optional)
   - **Contact Number** (optional)
   - **Email Address** (required, must be unique)
   - **Password** (at least 6 characters) and **Confirm Password**
3. Click **Create My Account**.
4. If successful, you will see a success message and be redirected to the **Login** page.

> **Tip:** Use a valid email address – you will use it to log in.

---

## 5. Login

1. Click **Login** in the navigation bar.
2. Enter your **Email** and **Password**.
3. Click **Login**.
4. On success, you are redirected to the **Home Page** and see a welcome message.

If you see “Invalid email or password”, double‑check your credentials or use the “Forgot password?” feature (not implemented – contact admin for reset).

---

## 6. Features for Registered Customers (After Login)

### 6.1 Write a Review

1. Go to the **Book Details** page of any book.
2. Below the book information, you will see a **Write a Review** button (only if you haven’t reviewed that book yet).
3. Select a **star rating** (1 to 5 stars).
4. Write your **comment** in the text area.
5. Click **Submit Review**.
6. Your review appears immediately, and you will see a success message.

> **Note:** You can only write **one review per book**. If you try to review the same book again, you will see an error message and a link to **Edit your review**.

### 6.2 Edit Your Own Review

1. Go to the **Book Details** page of the book you reviewed.
2. Below your review, click the **Edit** button (✏️).
3. Change the rating and/or comment.
4. Click **Save Changes**.
5. The updated review will appear.

You can also edit reviews from your **Dashboard**.

### 6.3 Delete Your Own Review

1. On the **Book Details** page, find your review.
2. Click the **Delete** button (🗑️).
3. A confirmation dialog will appear – click **OK** to confirm.
4. The review is removed immediately.

> **Warning:** Deletion is permanent. You cannot undo it.

### 6.4 View Your Dashboard

Click **Dashboard** in the navigation bar. Your dashboard shows:

- Your profile information (name, email, address, etc.)
- Two statistics: total reviews written and your average rating.
- A list of **all your reviews**, each with Edit and Delete buttons.

From the dashboard, you can quickly edit or delete any of your reviews.

### 6.5 Logout

Click **Logout** in the navigation bar. You will be logged out and redirected to the Home Page. Any attempt to access protected pages (like Dashboard or Write Review) will ask you to log in again.

---

## 7. Administrator Features

Administrators have all customer privileges plus the ability to **add new books**.

### 7.1 Add a New Book

1. Log in with an **admin account** (provided by your instructor or team leader).
2. In the navigation bar, you will see an **Add Book** link (only visible to admins).
3. Click **Add Book**.
4. Fill in the form:
   - **Book Title** (required)
   - **Author** (required)
   - **Category** (select from dropdown)
   - **Description** (optional)
   - **Image Filename** (e.g., `gatsby.jpg` – place the image in the `images/` folder first)
5. Click **Add Book to Library**.
6. The new book appears on the Home Page immediately.

> **Note:** Only administrators can add, edit, or delete books. If you are not an admin, the Add Book link will not appear.

---

## 8. Trending Books Section (Home Page)

On the Home Page, you will see a **“Trending Books This Week”** section.  
Books are ranked by the number of reviews received in the last 7 days. The most active books appear first.

This feature helps you discover popular books that other readers are talking about.

---

## 9. Troubleshooting

| Problem | Possible Solution |
|---------|-------------------|
| Cannot log in | Check that Caps Lock is off. Make sure you registered first. If you forgot your password, contact the administrator. |
| Review form does not appear | You must be logged in. Also, you cannot review a book twice – look for the “Edit your review” link instead. |
| Image not showing for a book | Ensure the image file name is spelled correctly and the file is uploaded to the `images/` folder. |
| “Access denied” message | You tried to access an admin page without admin privileges. Log in with an admin account. |
| Flash messages (success/error) not appearing | Refresh the page. If still missing, check your browser’s cookie settings. |
| Page looks broken (no styling) | Clear your browser cache or ensure the `css/style.css` file is correctly linked. |

---

## 10. System Requirements

- **Browser:** Latest version of Chrome, Firefox, Edge, or Safari.
- **Internet connection** (or local server access).
- **Screen resolution:** 1024×768 or higher recommended.

---

## 11. Support

For technical issues or questions, contact your course instructor or the development team:

- **Jasmine** – Frontend & Authentication  
- **Sebastian** – Reviews & Pages  
- **Joseph** – Database & Admin  

**Institution:** Divine Word University, Madang, Papua New Guinea  
**Unit:** IS312 Web Application Development

---

## 12. Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | May 2026 | Initial release – registration, login, review CRUD, admin add book, trending section. |

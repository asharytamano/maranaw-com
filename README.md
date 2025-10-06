# 🕌 MARANAW.COM
*A Digital Platform for Maranaw Knowledge, Faith, and Culture*

---

## 📖 Overview
**Maranaw.com** is a digital knowledge platform dedicated to preserving, teaching, and sharing the depth of **Maranaw Islamic scholarship**, culture, and language.  
It integrates modern web technologies with the timeless wisdom of **Qur’anic Tafsir**, **Fiqh**, and **customary Maranaw practices (Adat)**.

This repository contains the core files, scripts, and APIs that power the **Maranaw Tafsir Web System** — a foundational step toward building a full **Maranao-language Qur’an translation and Tafsir ecosystem** across web and mobile platforms.

---

## ⚙️ Project Components

| Component | Description |
|------------|--------------|
| **Frontend (Public Site)** | HTML, CSS, and JS structure for user-facing pages. |
| **Backend (API Folder)** | PHP endpoints that connect to the OpenAI API for question-answer features on Tafsir topics. |
| **`.env` File** | Stores environment variables such as your OpenAI API key (kept private and never uploaded to GitHub). |
| **`.gitignore` File** | Ensures sensitive files like `.env`, `config.php`, and log files are excluded from commits. |
| **Composer Dependencies** | Managed through `vlucas/phpdotenv` for secure environment loading. |

---

## 🔒 Security and Privacy
All API keys and database credentials are **loaded from environment variables**.  
Sensitive files such as `.env` and `config.php` are ignored via `.gitignore` to comply with GitHub’s **push protection** and **secret scanning** standards.

---

## 🧩 Installation & Setup

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/asharytamano/maranaw-com.git
cd maranaw-com

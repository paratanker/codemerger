# CodeMerger

**CodeMerger** is a lightweight Laravel-based internal tool that automates repetitive code synchronization between two separate Git repositories.

It was built to solve a real daily engineering problem: merging changes between an offshore team repository and a local team repository without constantly switching branches, pulling code, and manually copying files.

This project is intentionally simple, practical, and focused on productivity rather than being a generic merge solution.

---

## 🚀 Problem Statement

Manual workflow before CodeMerger:

* Switch between two repositories
* Pull matching branches
* Manually copy & overwrite files between folders
* Risk missing files or overwriting unintended changes

This process was time-consuming, repetitive, and error-prone.

---

## ✅ Solution

CodeMerger automates the **filesystem-level synchronization** between two repositories, reducing the workflow to a single controlled operation.

It does **not replace Git**. Instead, it removes repetitive human steps around Git-based workflows.

---

## 🧠 Key Capabilities

* Sync code between two separate repositories
* Reduce manual copy & paste operations
* Improve consistency in cross-team merges
* Designed for fast, local, internal usage

---

## 🛠 Tech Stack

* **Backend:** Laravel
* **Language:** PHP
* **Environment:** Laragon (Local)
* **Version Control:** Git

Laragon was chosen for its simplicity and speed for internal tooling.

---

## ⚙️ How It Works (High Level)

1. Configure source and target repositories
2. Define and match branches to synchronize
3. Preview and show the differences between changes
4. Run the merge operation
5. CodeMerger safely copies and overwrites files

---

## 📌 Project Status

* ✔ Usable for personal daily workflows
* ⚠ Not feature-complete
* ⚠ Minimal validation & edge-case handling
* ⚠ Conflicts must be resolved manually before running CodeMerger

The repository is public to demonstrate **engineering thinking**, not to present a production-ready product.

---

## 🎯 What This Project Demonstrates

* Identifying inefficiencies in real workflows
* Building internal automation tools
* Pragmatic Laravel application design
* Trade-off awareness (speed vs completeness)

---

## 🧩 Possible Improvements

* UI enhancement
* Conflicts detection
* Proper reporting
* Automatic server update

---

## ⚠️ Disclaimer

This tool was built for internal use and may not follow full production best practices. The focus is on solving a real problem efficiently.

---

## 👤 Author

**Shafiq Khalid**
Senior PHP Software Engineer

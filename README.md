# BurauenBiblio 📚🌴

## Project Credit & Intent

**BurauenBiblio** is a derivative work of the original **OpenBiblio** system, tailored for modern **PHP8.0** environments and localized school library needs in the **Philippines**.
Maintained as a community-driven, open-source initiative to revitalize and extend OpenBiblio's functionality designed to aid small libraries and schools.
It is named in honor of my hometown in Leyte, **Burauen**.

- OpenBiblio Authors:  
  **David Stevens, Joe Hagerty, Micah Stetson, Fred LaPlante**  
  (See LICENSE and copyright.html for full attribution)

- BurauenBiblio Maintainer: Ferdinand Tumulak 

📌 See [CHANGELOG.md](./CHANGELOG.md) for version history.

![Homepage](./readme_assets/opac_system2.webp "BurauenBiblio Homepage")

This repository includes a small set of sample bibliographic records (books) and fictional member accounts as part of the included SQL data.
These are provided solely for the purpose of demonstrating and testing the functionality of the OpenBiblio system.

The book entries are either fictional or based on public domain sources, and any associated thumbnail images are used under public domain or fair use for educational/demo purposes.

The member records are entirely fictitious and do not represent real individuals.

## ✅ PHP 8.0 Migration Notes

![Homepage](./readme_assets/actual_use_case_2.webp "BurauenBiblio Homepage")

The **BurauenBiblio system** is actively used at **Burauen Community College**, serving bibliographic search requests from both students and faculty. It currently manages a collection of nearly **5,000 bibliographic records**. The system now runs on **PHP 8.0**, offering substantial performance improvements over PHP 5.7—with speeds up to **2x to 4x faster**, depending on the workload and benchmark—all while continuing to operate on the same **legacy hardware**.

### 📌 Calendar Manager

![Homepage](./readme_assets/sample_calendar_image.webp "BurauenBiblio Homepage")

The system uses Calendar Logic to accurately calculate due dates based on your library’s open and closed days.

### 📌 Set limit on overdue charges

![Homepage](./readme_assets/overdue_notice.webp "BurauenBiblio Homepage")

Member types can be configured with **overdue charge** settings. When a patron exceeds the overdue limit accumulated from the penalty/day, they are **restricted** from making additional borrowings until the balance is settled.

### 📌 Card Catalog

![Homepage](./images/card_catalog_demo_sample.webp "BurauenBiblio Homepage")

The system includes support for **legacy library requirements** such as **Printed Card Catalog**.

### 📌 Z39.50 Online Metadata Retrieval

![Homepage](./readme_assets/metadata_retrieval.webp "BurauenBiblio Homepage")

Support for metadata retrieval process—particularly for cataloging by ISBN/LCCN using remote bibliographic sources.

### 🙌 Project Direction

![Homepage](./readme_assets/actual_use_case.webp "BurauenBiblio Homepage")

***This upgrade is part of my ongoing personal initiative to modernize the OpenBiblio codebase while maintaining backward compatibility**. It is now in its final stages, focusing on critical bug fixes, user interface enhancements, new and improved features, and full support for PHP 8.0. Further improvements—especially in UI/UX—are being developed, with planned integrations using more modern programming approaches and frontend technologies.*

This project is an ongoing modernization effort based on the original **OpenBiblio** system. It aims to improve performance, ensure compatibility with modern PHP versions, and enhance the overall user experience while maintaining support for legacy environments.

Contributions and testing feedback are genuinely welcome and appreciated—as they help sustain and grow this project further.

## 📦 Installation

📌 See [how_to_install.md](./how_to_install.md) for installation.

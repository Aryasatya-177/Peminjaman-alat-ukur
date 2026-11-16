# DESIGN.md - GaugeHub (Sistem Peminjaman & Inspeksi Alat Ukur Presisi)

## 1. Identity & Purpose
- **Nama Produk:** GaugeHub
- **Fungsi:** Sistem peminjaman, tracking stok, dan alur inspeksi/perbaikan alat ukur presisi metrologi industri (Dial Gauge, Bore Gauge, Micrometer, Vernier Caliper) untuk teknisi, pengguna bengkel/lab, dan administrator.
- **Karakter Visual:** Industrial Precision, Metrology-Grade Cleanliness, Instrument Engineering Confidence. Bukan generic SaaS template dengan gradien ungu neon, melainkan tampilan sistem instrumen presisi yang solid, tenang, dan sangat mudah dibaca di lingkungan bengkel dan kantor.

## 2. Dials (Taraf Energi, Ritme, Gerak)
- **ENERGY:** 2 (Fokus, profesional, terstruktur, tidak bising, tidak steril).
- **RHYTHM:** 2 (Hierarki visual jelas antara ringkasan metrik instrumen, antrian teknisi, dan tabel data peredaran alat).
- **MOTION:** 1 (Mikro-transisi fungsional 150ms-200ms pada tombol dan hover baris tabel, tanpa animasi bouncing atau distracting orbs).

## 3. Palet Warna (Slate & Steel Blue Precision)
- **Neutral Base:**
  - Canvas / Body Background: `#f8fafc` (Slate 50)
  - Surface / Card: `#ffffff` (Pure White)
  - Dark Surface (Sidebar & Shell): `#0f172a` (Slate 900) dengan border `#1e293b` (Slate 800)
  - Border: `#e2e8f0` (Slate 200) / Active Border `#cbd5e1` (Slate 300)
  - Primary Text: `#0f172a` (Slate 900 - Kontras tinggi > 12:1 WCAG AAA)
  - Muted Text: `#475569` (Slate 600 - Kontras 5.8:1 WCAG AA)
- **Primary Brand Color (Steel Blue):**
  - Primary: `#1e3a5f` (Deep Steel Blue)
  - Primary Hover: `#152943`
  - Primary Soft / Light: `#e0eaf6`
- **Deliberate Accent (Metrology Amber / Copper):**
  - Accent: `#b45309` (Amber 700) / Soft: `#fef3c7` (Digunakan secara hemat hanya pada indikasi penting, status perhatian, dan aksen brand).
- **Functional Status Indicators (Tidak dibuat pelangi acak):**
  - Ready / Selesai / Baik: `#059669` (Emerald 600) / Background `#ecfdf5`
  - Dipinjam / Perhatian: `#d97706` (Amber 600) / Background `#fffbeb`
  - Antrian Cek / Inspeksi: `#0284c7` (Sky 600) / Background `#f0f9ff`
  - Rusak / Kritis / Konfirmasi: `#dc2626` (Red 600) / Background `#fef2f2`

## 4. Typography & Spacing
- **Font:** Inter / System UI Sans
- **Hierarchy:**
  - H1 / Page Header: 1.5rem (24px), font-weight 700, tracking -0.02em
  - Section Header: 1.125rem (18px), font-weight 600
  - Body & Table: 0.875rem (14px), font-weight 400 & 500
  - Microcopy & Badges: 0.75rem (12px), font-weight 600
- **Border Radius:**
  - Controls & Buttons: `8px` (`0.5rem`)
  - Cards & Panels: `12px` (`0.75rem`)
  - Badges: `6px` (`0.375rem`) - *Bukan kapsul pill seragam!*

## 5. Anti-Slop Rules Compliance
- **R-01:** Menghapus seluruh gradien biru-ke-ungu generik (`#4f46e5` / `#6366f1`) dan background orbs.
- **R-02:** Tidak menggunakan karakter em dash (`—`) di seluruh teks antarmuka. Diganti tanda strip `-`, titik dua `:`, atau tanda kurung `()`.
- **R-03:** Layout mobile responsif penuh tanpa overflow, touch target minimal 44px.
- **R-04:** Ikon fungsional yang relevan dengan alat ukur dan instrumen metrologi.
- **R-12:** Shadow subtil berbasis elevasi nyata (`box-shadow: 0 1px 3px rgba(15,23,42,0.06), 0 1px 2px rgba(15,23,42,0.04)`), tanpa blur berlebihan.
- **R-14:** Kartu metrik berbobot informatif (label jelas, nilai metrologi, deskripsi status) tanpa icon raksasa transparan yang tidak berguna.
- **R-25 & R-32:** Memastikan rasio kontras teks minimal 4.5:1 (WCAG AA) dan indikator fokus keyboard terlihat jelas (`ring 2px steel-blue`).
- **R-27:** Menyediakan empty state, feedback validasi error, dan flash alerts yang jelas.

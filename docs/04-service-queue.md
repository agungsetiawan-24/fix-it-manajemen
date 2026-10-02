# 04. MODUL SERVIS & ALUR TEKNISI

## Alur Intake Unit Baru
1. Admin menginput data customer dan detail HP.
2. Mandatory Step: Checklist kondisi fisik dan fungsi HP awal sebelum HP dibongkar.
3. Cetak Bukti Tanda Terima (Nota).

## Alur Pengerjaan Teknisi
1. Teknisi mengambil tiket dari daftar `antrian`.
2. Jika ada kerusakan tambahan saat dibongkar:
   - Teknisi menambah sparepart tambahan di sistem.
   - Sistem merubah status menjadi `menunggu_approval` dan membekukan pengerjaan sampai customer menyetujui.

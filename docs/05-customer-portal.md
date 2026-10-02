# 05. MODUL CUSTOMER PORTAL & APPROVAL

## Akses Public
- URL: `/track/:kode_tiket` atau via scan QR Code pada nota.
- Tanpa Login (Public Read-Only & Action Specific).

## Fitur Approval Digital
- Jika status `menunggu_approval`, tampilkan Rincian Biaya Tambahan + Alasan Teknisi + Foto Komponen Rusak.
- Tombol Aksi: **[Setujui & Lanjutkan]** atau **[Tolak & Batalkan Servis]**.
- Integrasi Notifikasi: Kirim Webhook / Notifikasi WhatsApp jika terjadi perubahan status.

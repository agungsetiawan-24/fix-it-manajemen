# 02. SKEMA DATABASE & RELASI DATA

## Tabel Utama & Field

### 1. `users`
- `id` (UUID, Primary Key)
- `name` (String)
- `email` (String, Unique)
- `password` (String)
- `role` (Enum: 'admin', 'teknisi')
- `email_verified_at` (Timestamp, Nullable)
- `remember_token`
- `created_at`, `updated_at` (Timestamp)

### 2. `customers`
- `id` (UUID, Primary Key)
- `name` (String)
- `phone` (String, Indexed)
- `email` (String, Nullable)
- `address` (Text, Nullable)
- `created_at`, `updated_at` (Timestamp)

### 3. `service_tickets`
- `id` (UUID, Primary Key)
- `ticket_code` (String, Unique) -- Contoh: SRV-202610-001
- `customer_id` (UUID, FK -> customers.id, onUpdate cascade, onDelete restrict)
- `technician_id` (UUID, FK -> users.id, Nullable, onUpdate cascade, onDelete set null)
- `device_brand` (String) -- Contoh: 'Apple', 'Samsung', 'Xiaomi'
- `device_model` (String) -- Contoh: 'iPhone 13 Pro', 'Galaxy S23'
- `device_imei` (String, Nullable)
- `device_color` (String, Nullable)
- `encrypted_device_pin` (Text, Nullable) -- PIN/Pola layar dienkripsi
- `complaint_notes` (Text, Nullable) -- Keluhan kerusakan awal
- `technician_notes` (Text, Nullable) -- Catatan pengerjaan teknisi
- `status` (Enum: 'antrian', 'diagnosis', 'menunggu_approval', 'proses', 'testing_qc', 'siap_diambil', 'selesai', 'batal')
- `total_cost` (Decimal 15,2, Default: 0)
- `warranty_days` (Integer, Default: 0)
- `warranty_expiry_date` (Date, Nullable)
- `created_at`, `updated_at` (Timestamp)

### 4. `ticket_checklists`
- `id` (UUID, Primary Key)
- `ticket_id` (UUID, FK -> service_tickets.id, onUpdate cascade, onDelete cascade)
- `item_name` (String) -- Contoh: 'Layar/LCD', 'Kamera Depan', 'Face ID', 'Speaker', 'Baterai'
- `condition_before` (Enum: 'normal', 'rusak', 'baret', 'mati')
- `condition_after` (Enum: 'normal', 'rusak', 'baret', 'mati', Nullable)
- `notes` (Text, Nullable)
- `created_at`, `updated_at` (Timestamp)

### 5. `inventory_spareparts`
- `id` (UUID, Primary Key)
- `part_name` (String)
- `part_code` (String, Unique, Nullable)
- `category` (String, Nullable) -- Contoh: 'LCD', 'Baterai', 'Fleksibel', 'Kaca Kamera'
- `stock` (Integer, Default: 0)
- `min_stock` (Integer, Default: 5)
- `buy_price` (Decimal 15,2, Default: 0) -- Modal sparepart
- `sell_price` (Decimal 15,2, Default: 0) -- Harga jual / biaya ke customer
- `description` (Text, Nullable)
- `created_at`, `updated_at` (Timestamp)

### 6. `financial_transactions`
- `id` (UUID, Primary Key)
- `ticket_id` (UUID, FK -> service_tickets.id, Nullable, onUpdate cascade, onDelete set null)
- `user_id` (UUID, FK -> users.id, Nullable, onUpdate cascade, onDelete set null) -- Petugas / Kasir
- `type` (Enum: 'pemasukan_servis', 'pembelian_sparepart', 'pengeluaran_ops', 'penjualan_pos')
- `amount` (Decimal 15,2)
- `modal_amount` (Decimal 15,2, Default: 0)
- `net_profit` (Decimal 15,2, Default: 0)
- `payment_method` (String, Nullable) -- 'tunai', 'transfer', 'qris'
- `notes` (Text, Nullable)
- `created_at`, `updated_at` (Timestamp)

---

## Relasi Antar Model
1. **User (Teknisi / Admin):**
   - `hasMany(ServiceTicket::class, 'technician_id')`: Daftar tiket yang ditangani.
   - `hasMany(FinancialTransaction::class, 'user_id')`: Transaksi keuangan yang dicatat/dibuat.
2. **Customer:**
   - `hasMany(ServiceTicket::class, 'customer_id')`: Riwayat tiket servis milik customer.
3. **ServiceTicket:**
   - `belongsTo(Customer::class, 'customer_id')`: Pemilik unit HP yang diservis.
   - `belongsTo(User::class, 'technician_id')`: Teknisi yang ditugaskan.
   - `hasMany(TicketChecklist::class, 'ticket_id')`: Item-item checklist fisik awal & akhir.
   - `hasMany(FinancialTransaction::class, 'ticket_id')`: Transaksi keuangan terkait tiket ini.
4. **TicketChecklist:**
   - `belongsTo(ServiceTicket::class, 'ticket_id')`: Tiket servis induk.
5. **InventorySparepart:**
   - Dapat digunakan dalam pencatatan stok dan transaksi sparepart.
6. **FinancialTransaction:**
   - `belongsTo(ServiceTicket::class, 'ticket_id')`: Tiket servis terkait (jika ada).
   - `belongsTo(User::class, 'user_id')`: User/Kasir pencatat transaksi.

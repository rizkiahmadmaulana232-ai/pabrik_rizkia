# Pabrik_rizkia

Aplikasi web manajemen produksi pabrik dengan role:
- Admin
- Operator
- Engineering

## Fitur saat ini
- Autentikasi user per role.
- Manajemen job produksi.
- Scheduling produksi (cek bentrok mesin/operator).
- Monitoring progress dan laporan.
- Manajemen mesin dan user.
- Master sparepart + integrasi sparepart pada job produksi.

## Struktur utama
- `auth_rizkia/` : login, register, logout, forgot password.
- `admin_rizkia/` : dashboard, jobs, scheduling, mesin, users, laporan, monitoring, sparepart.
- `operator_rizkia/` : dashboard dan eksekusi kerja.
- `engineering_rizkia/` : dashboard dan status mesin.
- `config_rizkia/` : koneksi database.
- `pabrik_rizkia.sql` : dump struktur dan data database.

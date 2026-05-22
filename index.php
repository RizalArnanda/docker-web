<?php
$host     = 'mysql';
$dbname   = 'rizalarnanda';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='error-box'>Koneksi gagal: " . $conn->connect_error . "</div>");
}

// Handle tambah mahasiswa
$successMsg = '';
$errorMsg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama         = $conn->real_escape_string(trim($_POST['nama'] ?? ''));
    $nim          = $conn->real_escape_string(trim($_POST['nim'] ?? ''));
    $jenis_kelamin = $conn->real_escape_string(trim($_POST['jenis_kelamin'] ?? ''));

    if ($nama === '' || $nim === '' || $jenis_kelamin === '') {
        $errorMsg = 'Semua field wajib diisi.';
    } else {
        // Cek duplikat NIM
        $cek = $conn->query("SELECT id FROM mahasiswa WHERE nim = '$nim'");
        if ($cek && $cek->num_rows > 0) {
            $errorMsg = 'NIM <strong>' . htmlspecialchars($nim) . '</strong> sudah terdaftar.';
        } else {
            $insert = $conn->query("INSERT INTO mahasiswa (nim, nama, jenis_kelamin) VALUES ('$nim', '$nama', '$jenis_kelamin')");
            if ($insert) {
                $successMsg = 'Mahasiswa <strong>' . htmlspecialchars($nama) . '</strong> berhasil ditambahkan!';
            } else {
                $errorMsg = 'Gagal menambahkan data: ' . $conn->error;
            }
        }
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

if ($search !== '') {
    $sql = "SELECT * FROM mahasiswa WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' ORDER BY id ASC";
} else {
    $sql = "SELECT * FROM mahasiswa ORDER BY id ASC";
}

$result = $conn->query($sql);
$total  = $result ? $result->num_rows : 0;

// Hitung total semua (buat stat box, bukan hasil search)
$totalAll = $conn->query("SELECT COUNT(*) as c FROM mahasiswa")->fetch_assoc()['c'];
$totalL   = $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE jenis_kelamin='Laki-Laki'")->fetch_assoc()['c'];
$totalP   = $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE jenis_kelamin='Perempuan'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelas TPLE003 — Informasi Mahasiswa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg:        #f0f4f8;
      --surface:   #ffffff;
      --nav-bg:    #1b3a5c;
      --nav-text:  #a8c4e0;
      --nav-active:#5bc4f0;
      --primary:   #1e5f8e;
      --primary-dk:#154469;
      --text:      #1a2c3d;
      --text-muted:#5a7a94;
      --border:    #cfdded;
      --head-bg:   #e8f2fa;
      --row-hover: #f2f8fd;
      --badge-m-bg:#dff0fb;
      --badge-m-tx:#1a5c80;
      --badge-f-bg:#fde8f2;
      --badge-f-tx:#8a2060;
      --nim-bg:    #e2f4ef;
      --nim-tx:    #1a6b55;
      --radius:    10px;
      --success:   #1a8a55;
      --success-bg:#e6f7ef;
      --error:     #c0392b;
      --error-bg:  #fce8e8;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    /* NAV */
    nav {
      background: var(--nav-bg);
      padding: 0 40px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }

    .nav-brand { display: flex; align-items: center; gap: 12px; }

    .nav-logo {
      width: 42px; height: 42px;
      border-radius: 10px;
      background: linear-gradient(135deg, #5bc4f0, #2a9d8f);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .nav-logo svg { width: 24px; height: 24px; fill: white; }

    .nav-name { display: flex; flex-direction: column; gap: 2px; }
    .nav-name small {
      font-size: 9px; letter-spacing: 2.5px;
      text-transform: uppercase; color: var(--nav-text); opacity: 0.75;
    }
    .nav-name strong { font-size: 15px; font-weight: 700; color: #ffffff; letter-spacing: 0.3px; }

    /* HERO */
    .hero {
      background: var(--nav-bg);
      padding: 32px 40px 40px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute; top: -40px; right: -40px;
      width: 280px; height: 280px;
      border-radius: 50%;
      background: rgba(91,196,240,0.06);
    }

    .hero::after {
      content: '';
      position: absolute; bottom: -60px; right: 120px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(42,157,143,0.07);
    }

    .hero-inner {
      display: flex; align-items: flex-end;
      justify-content: space-between;
      position: relative; z-index: 1;
    }

    .hero h1 { font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; }
    .hero p { font-size: 13px; color: var(--nav-text); margin-top: 5px; letter-spacing: 0.5px; text-transform: uppercase; font-weight: 600; }

    .hero-stats { display: flex; gap: 12px; }

    .stat-box {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 10px;
      padding: 12px 20px;
      text-align: center;
      min-width: 90px;
    }
    .stat-box .num { font-size: 22px; font-weight: 700; color: var(--nav-active); }
    .stat-box .label { font-size: 10px; color: var(--nav-text); letter-spacing: 0.8px; text-transform: uppercase; margin-top: 2px; }

    /* WAVE */
    .wave { background: var(--nav-bg); line-height: 0; }
    .wave svg { display: block; width: 100%; }

    /* MAIN */
    main { padding: 24px 40px 60px; }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: 0 2px 16px rgba(30,95,142,0.07);
    }

    .toolbar {
      display: flex; align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      gap: 12px; flex-wrap: wrap;
    }

    .toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .search-wrap {
      display: flex; align-items: center; gap: 8px;
      background: var(--bg); border: 1px solid var(--border);
      border-radius: 8px; padding: 0 14px; width: 270px;
      transition: border-color .2s, background .2s;
    }
    .search-wrap:focus-within { border-color: var(--primary); background: #fff; }
    .search-wrap svg { color: var(--text-muted); flex-shrink: 0; }
    .search-wrap input {
      background: transparent; border: none; outline: none;
      color: var(--text); font-size: 13px; font-family: inherit;
      width: 100%; padding: 9px 0;
    }
    .search-wrap input::placeholder { color: var(--text-muted); }

    .btn-tambah {
      display: inline-flex; align-items: center; gap: 7px;
      background: linear-gradient(135deg, var(--primary), #2a9d8f);
      color: white; border: none; border-radius: 8px;
      padding: 9px 18px; font-size: 13px; font-weight: 600;
      font-family: inherit; cursor: pointer;
      box-shadow: 0 3px 10px rgba(30,95,142,0.3);
      transition: transform .15s, box-shadow .15s, opacity .15s;
      white-space: nowrap;
    }
    .btn-tambah:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(30,95,142,0.35); }
    .btn-tambah:active { transform: translateY(0); }
    .btn-tambah svg { flex-shrink: 0; }

    .total-label { font-size: 13px; color: var(--text-muted); }
    .total-label span { color: var(--primary); font-weight: 600; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: var(--head-bg); }

    th {
      padding: 13px 20px; font-size: 11px; font-weight: 700;
      letter-spacing: 1.2px; text-transform: uppercase;
      color: var(--primary-dk); text-align: left;
      border-bottom: 1px solid var(--border);
    }

    td {
      padding: 13px 20px; font-size: 14px;
      border-bottom: 1px solid #eaf2f8;
      color: var(--text); vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: var(--row-hover); }
    .no-cell { color: var(--text-muted); font-weight: 300; font-size: 13px; }

    .nim-pill {
      background: var(--nim-bg); color: var(--nim-tx);
      border: 1px solid #a8ddd0; border-radius: 6px;
      padding: 4px 10px; font-size: 12px; font-weight: 600; display: inline-block;
    }

    .gender-pill { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .gender-l { background: var(--badge-m-bg); color: var(--badge-m-tx); border: 1px solid #a8d4ed; }
    .gender-p { background: var(--badge-f-bg); color: var(--badge-f-tx); border: 1px solid #f0b8d8; }
    .gender-kosong { background: #f0f0f0; color: #999; border: 1px solid #ddd; }

    .empty-row td { text-align: center; padding: 48px; color: var(--text-muted); font-size: 14px; }

    .error-box {
      margin: 40px; padding: 16px 20px;
      background: #fce8e8; border: 1px solid #f5b8b8;
      color: #842222; border-radius: var(--radius); font-size: 14px;
    }

    /* ====== MODAL ====== */
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(10, 24, 40, 0.55);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      z-index: 500;
      display: flex; align-items: center; justify-content: center;
      opacity: 0; visibility: hidden;
      transition: opacity .25s ease, visibility .25s ease;
    }
    .modal-overlay.active { opacity: 1; visibility: visible; }

    .modal {
      background: var(--surface);
      border-radius: 16px;
      width: 100%; max-width: 460px;
      box-shadow: 0 20px 60px rgba(10,24,40,0.25), 0 0 0 1px rgba(255,255,255,0.1);
      overflow: hidden;
      transform: translateY(24px) scale(0.97);
      transition: transform .28s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-overlay.active .modal { transform: translateY(0) scale(1); }

    .modal-header {
      background: linear-gradient(135deg, var(--nav-bg), #154469);
      padding: 22px 24px 20px;
      display: flex; align-items: center; justify-content: space-between;
    }

    .modal-header-left { display: flex; align-items: center; gap: 12px; }

    .modal-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: rgba(91,196,240,0.2);
      border: 1px solid rgba(91,196,240,0.3);
      display: flex; align-items: center; justify-content: center;
    }
    .modal-icon svg { width: 20px; height: 20px; fill: var(--nav-active); }

    .modal-title { color: #fff; font-size: 16px; font-weight: 700; }
    .modal-subtitle { color: var(--nav-text); font-size: 12px; margin-top: 2px; }

    .modal-close {
      background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
      border-radius: 8px; width: 34px; height: 34px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--nav-text);
      transition: background .15s, color .15s;
    }
    .modal-close:hover { background: rgba(255,255,255,0.2); color: #fff; }
    .modal-close svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.5; fill: none; }

    .modal-body { padding: 24px; }

    /* Notif dalam modal */
    .notif {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 12px 14px; border-radius: 8px;
      font-size: 13px; margin-bottom: 18px;
      animation: slideDown .3s ease;
    }
    .notif-success { background: var(--success-bg); color: var(--success); border: 1px solid #a3d9be; }
    .notif-error   { background: var(--error-bg);   color: var(--error);   border: 1px solid #f5b8b8; }
    .notif svg { flex-shrink: 0; margin-top: 1px; }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-8px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .form-group { margin-bottom: 18px; }

    .form-label {
      display: block; font-size: 12px; font-weight: 700;
      letter-spacing: 0.8px; text-transform: uppercase;
      color: var(--primary-dk); margin-bottom: 7px;
    }

    .form-input {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--border); border-radius: 8px;
      font-size: 14px; font-family: inherit; color: var(--text);
      background: var(--bg);
      transition: border-color .2s, background .2s, box-shadow .2s;
      outline: none;
    }
    .form-input:focus {
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(30,95,142,0.1);
    }
    .form-input::placeholder { color: var(--text-muted); }

    /* Radio gender */
    .gender-options { display: flex; gap: 10px; }

    .gender-opt { flex: 1; }
    .gender-opt input[type="radio"] { display: none; }

    .gender-opt label {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 10px 14px; border-radius: 8px;
      border: 1.5px solid var(--border); background: var(--bg);
      font-size: 13px; font-weight: 600; cursor: pointer;
      transition: all .2s; color: var(--text-muted);
    }
    .gender-opt label svg { width: 16px; height: 16px; }

    .gender-opt input[type="radio"]:checked + label.laki {
      border-color: #1a5c80; background: var(--badge-m-bg); color: var(--badge-m-tx);
    }
    .gender-opt input[type="radio"]:checked + label.perempuan {
      border-color: #8a2060; background: var(--badge-f-bg); color: var(--badge-f-tx);
    }
    .gender-opt label:hover { border-color: var(--primary); background: #fff; }

    .modal-footer {
      padding: 16px 24px 24px;
      display: flex; gap: 10px; justify-content: flex-end;
    }

    .btn-cancel {
      padding: 10px 20px; border-radius: 8px;
      border: 1.5px solid var(--border); background: transparent;
      font-size: 13px; font-weight: 600; font-family: inherit;
      color: var(--text-muted); cursor: pointer;
      transition: background .15s, border-color .15s, color .15s;
    }
    .btn-cancel:hover { background: var(--bg); border-color: #9ab; color: var(--text); }

    .btn-submit {
      padding: 10px 22px; border-radius: 8px; border: none;
      background: linear-gradient(135deg, var(--primary), #2a9d8f);
      color: white; font-size: 13px; font-weight: 700;
      font-family: inherit; cursor: pointer;
      box-shadow: 0 3px 10px rgba(30,95,142,0.3);
      transition: transform .15s, box-shadow .15s;
      display: flex; align-items: center; gap: 7px;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(30,95,142,0.35); }
    .btn-submit:active { transform: translateY(0); }
    .btn-submit svg { width: 15px; height: 15px; fill: white; }

    /* Toast notif (sukses/error setelah modal nutup) */
    .toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 600;
      padding: 14px 18px; border-radius: 12px;
      font-size: 13px; font-weight: 500;
      display: flex; align-items: center; gap: 10px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      animation: toastIn .35s cubic-bezier(0.34, 1.56, 0.64, 1);
      max-width: 320px;
    }
    .toast-success { background: var(--success-bg); color: var(--success); border: 1px solid #a3d9be; }
    .toast-error   { background: var(--error-bg);   color: var(--error);   border: 1px solid #f5b8b8; }
    .toast svg { flex-shrink: 0; }

    @keyframes toastIn {
      from { opacity: 0; transform: translateY(16px) scale(0.95); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    footer {
      text-align: center; padding: 24px;
      font-size: 12px; color: var(--text-muted);
    }
  </style>
</head>
<body>

<!-- ===== NAV ===== -->
<nav>
  <div class="nav-brand">
    <div class="nav-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
      </svg>
    </div>
    <div class="nav-name">
      <small>Informasi Mahasiswa</small>
      <strong>Kelas TPLE003</strong>
    </div>
  </div>
</nav>

<!-- ===== HERO ===== -->
<div class="hero">
  <div class="hero-inner">
    <div>
      <h1>Data Mahasiswa</h1>
      <p>Kelas TPLE003</p>
    </div>
    <div class="hero-stats">
      <div class="stat-box">
        <div class="num"><?= $totalAll ?></div>
        <div class="label">Total</div>
      </div>
      <div class="stat-box">
        <div class="num"><?= $totalL ?></div>
        <div class="label">Laki-laki</div>
      </div>
      <div class="stat-box">
        <div class="num"><?= $totalP ?></div>
        <div class="label">Perempuan</div>
      </div>
    </div>
  </div>
</div>

<div class="wave">
  <svg viewBox="0 0 1440 40" preserveAspectRatio="none" height="40">
    <path fill="#f0f4f8" d="M0,40 C360,0 1080,0 1440,40 L1440,40 L0,40 Z"/>
  </svg>
</div>

<!-- ===== MAIN ===== -->
<main>
  <div class="card">
    <div class="toolbar">
      <div class="toolbar-left">
        <form method="GET" action="" style="display:contents;">
          <div class="search-wrap">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
              type="text" name="search"
              placeholder="Cari nama atau NIM..."
              value="<?= htmlspecialchars($search) ?>"
              oninput="this.form.submit()"
            />
          </div>
        </form>

        <!-- Tombol Tambah -->
        <button class="btn-tambah" onclick="openModal()">
          <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
          Tambah Mahasiswa
        </button>
      </div>

      <div class="total-label"><span><?= $total ?></span> mahasiswa ditemukan</div>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>NIM</th>
          <th>Nama Mahasiswa</th>
          <th>Jenis Kelamin</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($total === 0): ?>
          <tr class="empty-row">
            <td colspan="4">Tidak ada data yang ditemukan.</td>
          </tr>
        <?php else: ?>
          <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="no-cell"><?= $no++ ?></td>
            <td><span class="nim-pill"><?= htmlspecialchars($row['nim']) ?></span></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td>
              <?php
                $jk = isset($row['jenis_kelamin']) ? strtolower(trim($row['jenis_kelamin'])) : '';
                if ($jk === 'laki-laki') {
                    echo '<span class="gender-pill gender-l">Laki-Laki</span>';
                } elseif ($jk === 'perempuan') {
                    echo '<span class="gender-pill gender-p">Perempuan</span>';
                } else {
                    echo '<span class="gender-pill gender-kosong">-</span>';
                }
              ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<footer>
  &copy; <?= date('Y') ?> Informasi Mahasiswa &mdash; Kelas TPLE003
</footer>

<!-- ===== MODAL TAMBAH MAHASISWA ===== -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
  <div class="modal" id="modal">

    <div class="modal-header">
      <div class="modal-header-left">
        <div class="modal-icon">
          <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </div>
        <div>
          <div class="modal-title">Tambah Mahasiswa</div>
          <div class="modal-subtitle">Isi data dengan benar</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal()" type="button">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" onsubmit="handleSubmit(this)">
      <input type="hidden" name="action" value="tambah"/>

      <div class="modal-body">

        <?php if ($errorMsg !== ''): ?>
        <div class="notif notif-error">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span><?= $errorMsg ?></span>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label" for="nama">Nama Mahasiswa</label>
          <input
            class="form-input" type="text" id="nama" name="nama"
            placeholder="Masukkan nama lengkap..."
            value="<?= isset($_POST['nama']) && $errorMsg ? htmlspecialchars($_POST['nama']) : '' ?>"
            required autocomplete="off"
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="nim">NIM</label>
          <input
            class="form-input" type="text" id="nim" name="nim"
            placeholder="Masukkan NIM..."
            value="<?= isset($_POST['nim']) && $errorMsg ? htmlspecialchars($_POST['nim']) : '' ?>"
            required autocomplete="off"
          />
        </div>

        <div class="form-group">
          <label class="form-label">Jenis Kelamin</label>
          <div class="gender-options">
            <div class="gender-opt">
              <input type="radio" id="jk-l" name="jenis_kelamin" value="Laki-Laki"
                <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'Laki-Laki' && $errorMsg) ? 'checked' : '' ?>
              />
              <label for="jk-l" class="laki">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 9c0-2.8 2.2-5 5-5s5 2.2 5 5-2.2 5-5 5-5-2.2-5-5zm10 6.2V21h-2v-4h-2v4H8v-5.8C6.2 14.4 5 12.3 5 10H3c0 3.9 2.5 7.2 6 8.5V21h2v-2h2v2h2v-2.5c3.5-1.3 6-4.6 6-8.5h-2c0 2.3-1.2 4.4-3 5.5 0 0 0 0 0-.5z"/></svg>
                Laki-Laki
              </label>
            </div>
            <div class="gender-opt">
              <input type="radio" id="jk-p" name="jenis_kelamin" value="Perempuan"
                <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'Perempuan' && $errorMsg) ? 'checked' : '' ?>
              />
              <label for="jk-p" class="perempuan">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.2 2 7 4.2 7 7s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 12c-3.9 0-8 1.9-8 4v2h16v-2c0-2.1-4.1-4-8-4z"/></svg>
                Perempuan
              </label>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-submit">
          <svg viewBox="0 0 24 24"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
          Simpan Data
        </button>
      </div>

    </form>
  </div>
</div>

<!-- Toast -->
<?php if ($successMsg !== ''): ?>
<div class="toast toast-success" id="toastEl">
  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  <span><?= $successMsg ?></span>
</div>
<?php endif; ?>

<script>
  const overlay = document.getElementById('modalOverlay');

  function openModal() {
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('nama').focus(), 280);
  }

  function closeModal() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  function closeModalOutside(e) {
    if (e.target === overlay) closeModal();
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });

  // Buka modal otomatis kalau ada error supaya user tau
  <?php if ($errorMsg !== ''): ?>
  window.addEventListener('DOMContentLoaded', () => openModal());
  <?php endif; ?>

  // Auto-dismiss toast setelah 4 detik
  const toast = document.getElementById('toastEl');
  if (toast) {
    setTimeout(() => {
      toast.style.transition = 'opacity .4s, transform .4s';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 400);
    }, 4000);
  }

  function handleSubmit(form) {
    const btn = form.querySelector('.btn-submit');
    btn.innerHTML = `
      <svg viewBox="0 0 24 24" fill="white" width="15" height="15" style="animation:spin .7s linear infinite">
        <path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/>
      </svg>
      Menyimpan...
    `;
    btn.disabled = true;
  }
</script>

<style>
  @keyframes spin { to { transform: rotate(360deg); } }
</style>

</body>
</html>
<?php $conn->close(); ?>

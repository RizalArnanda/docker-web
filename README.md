<?php
$host     = 'db';
$dbname   = 'db_rizalarnanda';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='error-box'>Koneksi gagal: " . $conn->connect_error . "</div>");
}

$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

if ($search !== '') {
    $sql = "SELECT * FROM mahasiswa WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' ORDER BY id ASC";
} else {
    $sql = "SELECT * FROM mahasiswa ORDER BY id ASC";
}

$result = $conn->query($sql);
$total  = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelas TPLE003 — Data Mahasiswa</title>
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
      --accent:    #2a9d8f;
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

    .nav-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* LOGO */
    .nav-logo {
      width: 42px; height: 42px;
      border-radius: 10px;
      background: linear-gradient(135deg, #5bc4f0, #2a9d8f);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .nav-logo svg {
      width: 24px; height: 24px;
      fill: white;
    }

    .nav-name {
      display: flex; flex-direction: column; gap: 2px;
    }

    .nav-name small {
      font-size: 9px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--nav-text);
      opacity: 0.75;
    }

    .nav-name strong {
      font-size: 15px; font-weight: 700;
      color: #ffffff;
      letter-spacing: 0.3px;
    }

    .nav-links { display: flex; gap: 4px; }

    .nav-links a {
      text-decoration: none;
      color: var(--nav-text);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 7px 14px;
      border-radius: 6px;
      transition: background .15s, color .15s;
    }

    .nav-links a:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .nav-links a.active { color: var(--nav-active); background: rgba(91,196,240,0.12); }

    /* HERO */
    .hero {
      background: var(--nav-bg);
      padding: 32px 40px 40px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 280px; height: 280px;
      border-radius: 50%;
      background: rgba(91,196,240,0.06);
    }

    .hero::after {
      content: '';
      position: absolute;
      bottom: -60px; right: 120px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(42,157,143,0.07);
    }

    .hero-inner {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 28px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.3px;
    }

    .hero p {
      font-size: 13px;
      color: var(--nav-text);
      margin-top: 5px;
      letter-spacing: 0.3px;
    }

    .hero-stats {
      display: flex;
      gap: 12px;
    }

    .stat-box {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 10px;
      padding: 12px 20px;
      text-align: center;
      min-width: 90px;
    }

    .stat-box .num {
      font-size: 22px;
      font-weight: 700;
      color: var(--nav-active);
    }

    .stat-box .label {
      font-size: 10px;
      color: var(--nav-text);
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-top: 2px;
    }

    /* WAVE */
    .wave {
      background: var(--nav-bg);
      line-height: 0;
    }

    .wave svg {
      display: block;
      width: 100%;
    }

    /* MAIN */
    main { padding: 24px 40px 60px; }

    /* CARD */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: 0 2px 16px rgba(30,95,142,0.07);
    }

    /* TOOLBAR */
    .toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      gap: 12px;
      flex-wrap: wrap;
    }

    .search-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 0 14px;
      width: 270px;
      transition: border-color .2s, background .2s;
    }

    .search-wrap:focus-within {
      border-color: var(--primary);
      background: #fff;
    }

    .search-wrap svg { color: var(--text-muted); flex-shrink: 0; }

    .search-wrap input {
      background: transparent;
      border: none;
      outline: none;
      color: var(--text);
      font-size: 13px;
      font-family: inherit;
      width: 100%;
      padding: 9px 0;
    }

    .search-wrap input::placeholder { color: var(--text-muted); }

    .total-label { font-size: 13px; color: var(--text-muted); }
    .total-label span { color: var(--primary); font-weight: 600; }

    /* TABLE */
    table { width: 100%; border-collapse: collapse; }

    thead tr { background: var(--head-bg); }

    th {
      padding: 13px 20px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: var(--primary-dk);
      text-align: left;
      border-bottom: 1px solid var(--border);
    }

    td {
      padding: 13px 20px;
      font-size: 14px;
      border-bottom: 1px solid #eaf2f8;
      color: var(--text);
      vertical-align: middle;
    }

    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: var(--row-hover); }

    .no-cell { color: var(--text-muted); font-weight: 300; font-size: 13px; }

    .nim-pill {
      background: var(--nim-bg);
      color: var(--nim-tx);
      border: 1px solid #a8ddd0;
      border-radius: 6px;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.3px;
      display: inline-block;
    }

    .gender-pill {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .gender-l {
      background: var(--badge-m-bg);
      color: var(--badge-m-tx);
      border: 1px solid #a8d4ed;
    }

    .gender-p {
      background: var(--badge-f-bg);
      color: var(--badge-f-tx);
      border: 1px solid #f0b8d8;
    }

    .empty-row td {
      text-align: center;
      padding: 48px;
      color: var(--text-muted);
      font-size: 14px;
    }

    .error-box {
      margin: 40px;
      padding: 16px 20px;
      background: #fce8e8;
      border: 1px solid #f5b8b8;
      color: #842222;
      border-radius: var(--radius);
      font-size: 14px;
    }

    /* FOOTER */
    footer {
      text-align: center;
      padding: 24px;
      font-size: 12px;
      color: var(--text-muted);
      letter-spacing: 0.3px;
    }
  </style>
</head>
<body>

<nav>
  <div class="nav-brand">
    <div class="nav-logo">
      <!-- Logo: graduation cap -->
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
      </svg>
    </div>
    <div class="nav-name">
      <small>Portal Akademik</small>
      <strong>Kelas TPLE003</strong>
    </div>
  </div>
  <div class="nav-links">
    <a href="#">Beranda</a>
    <a href="#" class="active">Data Mahasiswa</a>
  </div>
</nav>

<div class="hero">
  <div class="hero-inner">
    <div>
      <h1>Data Mahasiswa</h1>
      <p>Informasi peserta kelas TPLE003</p>
    </div>
    <div class="hero-stats">
      <div class="stat-box">
        <div class="num"><?= $total ?></div>
        <div class="label">Mahasiswa</div>
      </div>
      <div class="stat-box">
        <div class="num">TPLE003</div>
        <div class="label">Kelas</div>
      </div>
    </div>
  </div>
</div>

<div class="wave">
  <svg viewBox="0 0 1440 40" preserveAspectRatio="none" height="40">
    <path fill="#f0f4f8" d="M0,40 C360,0 1080,0 1440,40 L1440,40 L0,40 Z"/>
  </svg>
</div>

<main>
  <div class="card">
    <div class="toolbar">
      <form method="GET" action="" style="display:contents;">
        <div class="search-wrap">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input
            type="text"
            name="search"
            placeholder="Cari nama atau NIM..."
            value="<?= htmlspecialchars($search) ?>"
            oninput="this.form.submit()"
          />
        </div>
      </form>
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
              <?php if (strtolower($row['jenis_kelamin']) === 'laki-laki'): ?>
                <span class="gender-pill gender-l">Laki-Laki</span>
              <?php else: ?>
                <span class="gender-pill gender-p">Perempuan</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<footer>
  &copy; <?= date('Y') ?> Kelas TPLE003 &mdash; Portal Akademik
</footer>

</body>
</html>
<?php $conn->close(); ?>

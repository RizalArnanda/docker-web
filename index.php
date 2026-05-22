<?php
$host     = 'mysql';
$dbname   = 'db_rizalarnanda';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<div class='error-box'>Koneksi gagal: " . $conn->connect_error . "</div>");
}

$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';

if ($search !== '') {
    $sql = "SELECT * FROM mahasiswa 
            WHERE nim LIKE '%$search%' 
            OR nama LIKE '%$search%' 
            ORDER BY id ASC";
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
  <title>Data Mahasiswa</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>

    :root {
      --bg: #f0f4f8;
      --surface: #ffffff;
      --nav-bg: #1b3a5c;
      --nav-text: #a8c4e0;
      --nav-active: #5bc4f0;
      --primary: #1e5f8e;
      --primary-dk: #154469;
      --accent: #2a9d8f;
      --text: #1a2c3d;
      --text-muted: #5a7a94;
      --border: #cfdded;
      --head-bg: #e8f2fa;
      --row-hover: #f2f8fd;
      --badge-m-bg: #dff0fb;
      --badge-m-tx: #1a5c80;
      --badge-f-bg: #fde8f2;
      --badge-f-tx: #8a2060;
      --nim-bg: #e2f4ef;
      --nim-tx: #1a6b55;
      --radius: 10px;
    }

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
    }

    body{
      font-family:'Plus Jakarta Sans',sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    nav{
      background:var(--nav-bg);
      padding:0 40px;
      height:64px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      box-shadow:0 2px 12px rgba(0,0,0,0.2);
    }

    .nav-brand{
      display:flex;
      align-items:center;
      gap:12px;
    }

    .nav-logo{
      width:42px;
      height:42px;
      border-radius:10px;
      background:linear-gradient(135deg,#5bc4f0,#2a9d8f);
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .nav-logo svg{
      width:24px;
      height:24px;
      fill:white;
    }

    .nav-name small{
      font-size:9px;
      letter-spacing:2px;
      color:var(--nav-text);
      text-transform:uppercase;
    }

    .nav-name strong{
      font-size:15px;
      color:white;
    }

    .nav-links a{
      text-decoration:none;
      color:var(--nav-text);
      font-size:11px;
      font-weight:600;
      margin-left:15px;
    }

    .nav-links a.active{
      color:var(--nav-active);
    }

    .hero{
      background:var(--nav-bg);
      padding:35px 40px 50px;
    }

    .hero-inner{
      display:flex;
      justify-content:space-between;
      align-items:end;
    }

    .hero h1{
      color:white;
      font-size:30px;
    }

    .hero p{
      color:var(--nav-text);
      margin-top:5px;
    }

    .hero-stats{
      display:flex;
      gap:12px;
    }

    .stat-box{
      background:rgba(255,255,255,0.08);
      border:1px solid rgba(255,255,255,0.1);
      border-radius:10px;
      padding:12px 20px;
      text-align:center;
    }

    .num{
      color:var(--nav-active);
      font-size:22px;
      font-weight:700;
    }

    .label{
      color:var(--nav-text);
      font-size:10px;
      text-transform:uppercase;
      margin-top:3px;
    }

    main{
      padding:30px 40px 60px;
    }

    .card{
      background:white;
      border-radius:10px;
      overflow:hidden;
      border:1px solid var(--border);
      box-shadow:0 2px 16px rgba(30,95,142,0.07);
    }

    .toolbar{
      padding:18px 20px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      border-bottom:1px solid var(--border);
    }

    .search-wrap{
      display:flex;
      align-items:center;
      gap:8px;
      background:var(--bg);
      border:1px solid var(--border);
      border-radius:8px;
      padding:0 14px;
      width:270px;
    }

    .search-wrap input{
      border:none;
      outline:none;
      background:transparent;
      padding:10px 0;
      width:100%;
      font-family:inherit;
    }

    table{
      width:100%;
      border-collapse:collapse;
    }

    thead{
      background:var(--head-bg);
    }

    th{
      padding:14px 20px;
      text-align:left;
      font-size:11px;
      letter-spacing:1px;
      text-transform:uppercase;
      color:var(--primary-dk);
    }

    td{
      padding:14px 20px;
      border-top:1px solid #edf2f7;
    }

    tbody tr:hover{
      background:var(--row-hover);
    }

    .nim-pill{
      background:var(--nim-bg);
      color:var(--nim-tx);
      padding:5px 10px;
      border-radius:6px;
      font-size:12px;
      font-weight:600;
    }

    .gender-pill{
      padding:5px 12px;
      border-radius:20px;
      font-size:12px;
      font-weight:600;
    }

    .gender-l{
      background:var(--badge-m-bg);
      color:var(--badge-m-tx);
    }

    .gender-p{
      background:var(--badge-f-bg);
      color:var(--badge-f-tx);
    }

    .empty-row td{
      text-align:center;
      padding:40px;
      color:var(--text-muted);
    }

    footer{
      text-align:center;
      padding:25px;
      font-size:12px;
      color:var(--text-muted);
    }

  </style>
</head>

<body>

<nav>

  <div class="nav-brand">

    <div class="nav-logo">
      <svg viewBox="0 0 24 24">
        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
      </svg>
    </div>

    <div class="nav-name">
      <small>Sistem Informasi Mahasiswa</small>
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

<main>

  <div class="card">

    <div class="toolbar">

      <form method="GET" action="" style="display:contents;">

        <div class="search-wrap">

          <input
            type="text"
            name="search"
            placeholder="Cari nama atau NIM..."
            value="<?= htmlspecialchars($search) ?>"
            oninput="this.form.submit()"
          />

        </div>

      </form>

      <div>
        <strong><?= $total ?></strong> mahasiswa ditemukan
      </div>

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
          <td colspan="4">Tidak ada data ditemukan.</td>
        </tr>

      <?php else: ?>

        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>

        <tr>

          <td><?= $no++ ?></td>

          <td>
            <span class="nim-pill">
              <?= htmlspecialchars($row['nim']) ?>
            </span>
          </td>

          <td><?= htmlspecialchars($row['nama']) ?></td>

          <td>

          <?php if (strtolower($row['jenis_kelamin']) == 'laki-laki'): ?>

            <span class="gender-pill gender-l">
              Laki-Laki
            </span>

          <?php else: ?>

            <span class="gender-pill gender-p">
              Perempuan
            </span>

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
  &copy; <?= date('Y') ?> — Sistem Informasi Mahasiswa
</footer>

</body>
</html>

<?php $conn->close(); ?>

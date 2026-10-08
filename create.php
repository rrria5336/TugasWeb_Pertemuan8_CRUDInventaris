<?php
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

// 🍼 Ambil semua kategori & supplier untuk dropdown
$stmtKat = $db->prepare("SELECT * FROM kategori ORDER BY nama_kategori");
$stmtKat->execute();
$kategoris = $stmtKat->fetchAll();

$stmtSup = $db->prepare("SELECT * FROM supplier ORDER BY nama_supplier");
$stmtSup->execute();
$suppliers = $stmtSup->fetchAll();

// 🍼 Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_produk']);
    $kategori  = (int)$_POST['kategori_id'];
    $supplier  = (int)$_POST['supplier_id'];
    $harga     = (float)$_POST['harga'];
    $stok      = (int)$_POST['stok'];
    $deskripsi = trim($_POST['deskripsi']);

    // 🍼 Validasi sederhana
    if (empty($nama) || $kategori <= 0 || $supplier <= 0 || $harga <= 0) {
        setFlash('danger', '🍼 Data tidak lengkap! Isi semua field yang wajib.');
        redirect('create.php');
    }

    // 🍼 Insert dengan prepared statement
    $sql = "INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok, deskripsi)
            VALUES (:nama, :kategori, :supplier, :harga, :stok, :deskripsi)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nama'      => $nama,
        ':kategori'  => $kategori,
        ':supplier'  => $supplier,
        ':harga'     => $harga,
        ':stok'      => $stok,
        ':deskripsi' => $deskripsi,
    ]);

    setFlash('success', '🍼 Produk berhasil ditambahkan!');
    redirect('index.php');
}
?>

<h2>➕ Tambah Produk Baru</h2>

<div class="form-card">
    <form method="POST" action="">
        <div class="form-group">
            <label>Nama Produk *</label>
            <input type="text" name="nama_produk" required
                   placeholder="Masukkan nama produk"
                   value="<?= isset($_POST['nama_produk']) ? e($_POST['nama_produk']) : '' ?>">
        </div>

        <div class="form-group">
            <label>Kategori *</label>
            <select name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $k['id']) ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Supplier *</label>
            <select name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"
                        <?= (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $s['id']) ? 'selected' : '' ?>>
                        <?= e($s['nama_supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="harga" min="0" step="100" required
                   placeholder="Contoh: 50000"
                   value="<?= isset($_POST['harga']) ? e($_POST['harga']) : '' ?>">
        </div>

        <div class="form-group">
            <label>Stok *</label>
            <input type="number" name="stok" min="0" required
                   placeholder="Jumlah stok"
                   value="<?= isset($_POST['stok']) ? e($_POST['stok']) : '0' ?>">
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                      placeholder="Deskripsi produk (opsional)"><?= isset($_POST['deskripsi']) ? e($_POST['deskripsi']) : '' ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">💾 Simpan Produk</button>
            <a href="index.php" class="btn btn-danger">❌ Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
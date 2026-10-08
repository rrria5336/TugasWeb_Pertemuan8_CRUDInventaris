<?php
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

// 🍼 Cek apakah ada ID
if (!isset($_GET['id'])) {
    setFlash('danger', '🍼 ID produk tidak ditemukan!');
    redirect('index.php');
}

$id = (int)$_GET['id'];

// 🍼 Ambil data produk berdasarkan ID
$stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

// 🍼 Kalau produk nggak ada
if (!$produk) {
    setFlash('danger', '🍼 Produk tidak ditemukan!');
    redirect('index.php');
}

// 🍼 Ambil kategori & supplier untuk dropdown
$stmtKat = $db->prepare("SELECT * FROM kategori ORDER BY nama_kategori");
$stmtKat->execute();
$kategoris = $stmtKat->fetchAll();

$stmtSup = $db->prepare("SELECT * FROM supplier ORDER BY nama_supplier");
$stmtSup->execute();
$suppliers = $stmtSup->fetchAll();

// 🍼 Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_produk']);
    $kategori  = (int)$_POST['kategori_id'];
    $supplier  = (int)$_POST['supplier_id'];
    $harga     = (float)$_POST['harga'];
    $stok      = (int)$_POST['stok'];
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama) || $kategori <= 0 || $supplier <= 0 || $harga <= 0) {
        setFlash('danger', '🍼 Data tidak lengkap!');
        redirect("update.php?id=$id");
    }

    $sql = "UPDATE produk
            SET nama_produk = :nama,
                kategori_id = :kategori,
                supplier_id = :supplier,
                harga = :harga,
                stok = :stok,
                deskripsi = :deskripsi
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nama'      => $nama,
        ':kategori'  => $kategori,
        ':supplier'  => $supplier,
        ':harga'     => $harga,
        ':stok'      => $stok,
        ':deskripsi' => $deskripsi,
        ':id'        => $id,
    ]);

    setFlash('success', ' Produk berhasil diupdate!');
    redirect('index.php');
}
?>

<h2>✏️ Edit Produk</h2>

<div class="form-card">
    <form method="POST" action="">
        <div class="form-group">
            <label>Nama Produk *</label>
            <input type="text" name="nama_produk" required
                   value="<?= e($produk['nama_produk']) ?>">
        </div>

        <div class="form-group">
            <label>Kategori *</label>
            <select name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= $produk['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
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
                        <?= $produk['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['nama_supplier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" name="harga" min="0" step="100" required
                   value="<?= e($produk['harga']) ?>">
        </div>

        <div class="form-group">
            <label>Stok *</label>
            <input type="number" name="stok" min="0" required
                   value="<?= e($produk['stok']) ?>">
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="3"><?= e($produk['deskripsi']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-warning"> Update Produk</button>
            <a href="index.php" class="btn btn-danger">❌ Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
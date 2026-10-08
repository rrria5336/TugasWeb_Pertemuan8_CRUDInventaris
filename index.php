<?php
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

// 🍼 Ambil data produk dengan JOIN kategori & supplier
$sql = "SELECT p.*, k.nama_kategori, s.nama_supplier
        FROM produk p
        JOIN kategori k ON p.kategori_id = k.id
        JOIN supplier s ON p.supplier_id = s.id
        ORDER BY p.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$produk = $stmt->fetchAll();
?>

<h2>📦 Daftar Produk Inventaris</h2>

<?php
// 🍼 Tampilkan flash message
$flash = getFlash();
if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produk)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:30px;">
                        🍼 Belum ada produk. Yuk tambah!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($produk as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= e($p['nama_produk']) ?></strong></td>
                    <td><?= e($p['nama_kategori']) ?></td>
                    <td><?= e($p['nama_supplier']) ?></td>
                    <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                    <td>
                        <?php
                        $stokClass = $p['stok'] <= 10 ? 'badge-low'
                                   : ($p['stok'] <= 50 ? 'badge-mid' : 'badge-high');
                        ?>
                        <span class="badge <?= $stokClass ?>"><?= $p['stok'] ?></span>
                    </td>
                    <td>
                        <a href="update.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('🍼 Yakin mau hapus produk ini?')">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
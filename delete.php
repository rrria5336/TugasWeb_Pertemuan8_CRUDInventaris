<?php
require_once 'includes/header.php';

$db = Database::getInstance()->getConnection();

// 🍼 Cek ID
if (!isset($_GET['id'])) {
    setFlash('danger', '🍼 ID produk tidak ditemukan!');
    redirect('index.php');
}

$id = (int)$_GET['id'];

// 🍼 Cek apakah produk ada
$stmt = $db->prepare("SELECT nama_produk FROM produk WHERE id = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    setFlash('danger', '🍼 Produk tidak ditemukan!');
    redirect('index.php');
}

// 🍼 Hapus produk
$stmt = $db->prepare("DELETE FROM produk WHERE id = :id");
$stmt->execute([':id' => $id]);

setFlash('success', "🍼 Produk '" . $produk['nama_produk'] . "' berhasil dihapus!");
redirect('index.php');
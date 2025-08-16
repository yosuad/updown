<?php
require_once(__DIR__ . '/system/core.class.php');

function sizeFormat($bytes) {
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;

    if ($bytes < $kb) return $bytes . ' B';
    if ($bytes < $mb) return ceil($bytes / $kb) . ' KB';
    if ($bytes < $gb) return ceil($bytes / $mb) . ' MB';
    if ($bytes < $tb) return ceil($bytes / $gb) . ' GB';
    return ceil($bytes / $tb) . ' TB';
}

$core = new Core();
$baseDir = '/home/u383547040/domains/wiquiweb.com/public_html/files/uploads/';

if (!isset($_GET['file'])) die('Archivo no especificado.');

$filename = base64_decode($_GET['file']);
$filepath = $baseDir . basename($filename);

if (!file_exists($filepath)) die("El archivo no existe: " . htmlspecialchars($filepath));

$filesize = filesize($filepath);
$page = $_GET['page'] ?? 'show';

if ($page === 'dl') {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Content-Length: ' . $filesize);
    flush();
    readfile($filepath);
    exit;
}

$fileLink = "download.php?page=dl&file=" . urlencode($_GET['file']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= app_name ?> - Download</title>
    <link rel="stylesheet" type="text/css" href="assets/main.css">
    <style>
        .download-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.3s ease;
        }
        .download-btn:hover {
            background-color: #45a049;
        }
        .download-btn:disabled {
            background-color: #888;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <img src="assets/images/logo.png" alt="Logo">
    <div class="download-area">
        <button id="downloadBtn" data-timer="<?= waitfor ?>" class="download-btn">
            Download&nbsp;[<?= sizeFormat($filesize) ?>]
        </button>
        <ul>
            <li>Report files: <a href="mailto:<?= app_contact_email ?>"><?= app_contact_email ?></a></li>
        </ul>
    </div>
</div>

<script>
const downloadBtn = document.getElementById('downloadBtn');
const fileLink = "<?= $fileLink ?>";

downloadBtn.addEventListener('click', function() {
    let timer = parseInt(this.dataset.timer);
    downloadBtn.disabled = true;
    const interval = setInterval(() => {
        if (timer > 0) {
            downloadBtn.textContent = `Your download will begin in ${timer} seconds`;
            timer--;
        } else {
            clearInterval(interval);
            window.location.href = fileLink;
            downloadBtn.textContent = "Your file is downloading...";
        }
    }, 1000);
});
</script>
</body>
</html>

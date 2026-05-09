<?php
session_start();

if (empty($_SESSION['shop_id'])) {
  header('Location: index.php');
  exit;
}

$shop_id   = $_SESSION['shop_id'];
$shop_name = $_SESSION['shop_name'];
$json_path = __DIR__ . '/../congestion.json';

$data = json_decode(file_get_contents($json_path), true);
$current_status  = $data[$shop_id]['status']  ?? 'low';
$current_message = $data[$shop_id]['message'] ?? '';

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 混み具合
  if (isset($_POST['status']) && in_array($_POST['status'], ['low', 'mid', 'high'])) {
    $data[$shop_id]['status'] = $_POST['status'];
    $current_status = $_POST['status'];
  }
  // メッセージ（140文字まで）
  $msg = mb_substr(trim($_POST['message'] ?? ''), 0, 140);
  $data[$shop_id]['message'] = $msg;
  $data[$shop_id]['updated'] = date('H:i');
  file_put_contents($json_path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
  $current_message = $msg;
  $success = true;
}

$status_labels = ['low' => '空いてる', 'mid' => '普通', 'high' => '混んでる'];
$status_colors = ['low' => '#2e7d32', 'mid' => '#e65100', 'high' => '#c62828'];
$status_bg     = ['low' => '#e8f5e9', 'mid' => '#fff8e1', 'high' => '#ffebee'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理画面｜<?= htmlspecialchars($shop_name) ?></title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
      background: #f5f7fa; min-height: 100vh; padding: 1.5rem 1rem;
    }
    header {
      background: #1a3a5c; color: #fff; padding: 0.9rem 1.5rem;
      border-radius: 10px; margin-bottom: 1.5rem;
      display: flex; justify-content: space-between; align-items: center;
      max-width: 480px; margin-left: auto; margin-right: auto;
    }
    header h1 { font-size: 15px; }
    header .shop { font-size: 12px; color: #8fcb9b; margin-top: 2px; }
    .logout {
      font-size: 12px; color: rgba(255,255,255,0.7);
      background: rgba(255,255,255,0.12); border: none;
      padding: 5px 12px; border-radius: 20px; cursor: pointer; text-decoration: none;
    }
    .logout:hover { background: rgba(255,255,255,0.2); color: #fff; }
    .card {
      background: #fff; border-radius: 12px; padding: 2rem 1.5rem;
      box-shadow: 0 4px 24px rgba(0,0,0,0.07);
      max-width: 480px; margin: 0 auto;
    }
    .success {
      background: #e8f5e9; color: #2e7d32; border-radius: 8px;
      padding: 0.75rem 1rem; font-size: 14px; margin-bottom: 1.25rem;
      text-align: center; font-weight: bold;
    }
    .current-label { font-size: 13px; color: #888; margin-bottom: 8px; }
    .current-badge {
      display: inline-block; font-size: 18px; font-weight: bold;
      padding: 6px 20px; border-radius: 30px; margin-bottom: 1.75rem;
    }
    .section-title { font-size: 14px; font-weight: bold; color: #1a3a5c; margin-bottom: 0.75rem; }
    .status-btns { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 1.5rem; }
    .status-btn {
      padding: 1rem 0.5rem; border-radius: 8px; border: 2px solid transparent;
      font-size: 15px; font-weight: bold; cursor: pointer;
      transition: transform 0.1s, box-shadow 0.1s; text-align: center;
    }
    .status-btn:active { transform: scale(0.97); }
    .status-btn.selected { border-color: #1a3a5c; box-shadow: 0 0 0 3px rgba(26,58,92,0.15); }
    .btn-low  { background: #e8f5e9; color: #2e7d32; }
    .btn-mid  { background: #fff8e1; color: #e65100; }
    .btn-high { background: #ffebee; color: #c62828; }
    .divider { border: none; border-top: 1px solid #eee; margin: 1.5rem 0; }

    /* メッセージ欄 */
    .msg-wrap { margin-bottom: 1.5rem; }
    .msg-examples {
      display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 0.75rem;
    }
    .msg-example {
      font-size: 12px; background: #f0f4f8; color: #1a3a5c;
      border: 1px solid #dde5ef; border-radius: 20px;
      padding: 3px 10px; cursor: pointer; transition: background 0.15s;
    }
    .msg-example:hover { background: #dde5ef; }
    textarea {
      width: 100%; padding: 0.75rem; border: 1px solid #dde5ef; border-radius: 8px;
      font-size: 14px; font-family: inherit; resize: vertical; min-height: 80px;
      transition: border-color 0.2s;
    }
    textarea:focus { outline: none; border-color: #1a3a5c; }
    .char-count { font-size: 11px; color: #aaa; text-align: right; margin-top: 4px; }

    .submit-btn {
      width: 100%; padding: 0.85rem; background: #1a3a5c; color: #fff;
      border: none; border-radius: 8px; font-size: 16px; font-weight: bold;
      cursor: pointer; transition: background 0.2s;
    }
    .submit-btn:hover { background: #254f7a; }
    .back { text-align: center; font-size: 13px; }
    .back a { color: #1a3a5c; text-decoration: underline; }
  </style>
</head>
<body>

<header style="margin-bottom:1.5rem;">
  <div>
    <div style="font-size:11px;color:#8fcb9b;letter-spacing:0.1em;">ADMIN</div>
    <h1>混み具合 管理画面</h1>
    <div class="shop"><?= htmlspecialchars($shop_name) ?></div>
  </div>
  <a href="logout.php" class="logout">ログアウト</a>
</header>

<div class="card">

  <?php if ($success): ?>
    <div class="success">✅ 更新しました！（<?= date('H:i') ?>）</div>
  <?php endif; ?>

  <div class="current-label">現在の混み具合</div>
  <div class="current-badge" style="background:<?= $status_bg[$current_status] ?>;color:<?= $status_colors[$current_status] ?>;">
    <?= $status_labels[$current_status] ?>
  </div>

  <form method="POST" id="statusForm">
    <div class="section-title">混み具合を選択</div>
    <div class="status-btns">
      <button type="button" name="status" value="low"
        class="status-btn btn-low <?= $current_status === 'low' ? 'selected' : '' ?>"
        onclick="selectStatus('low')">😊<br>空いてる</button>
      <button type="button" name="status" value="mid"
        class="status-btn btn-mid <?= $current_status === 'mid' ? 'selected' : '' ?>"
        onclick="selectStatus('mid')">🙂<br>普通</button>
      <button type="button" name="status" value="high"
        class="status-btn btn-high <?= $current_status === 'high' ? 'selected' : '' ?>"
        onclick="selectStatus('high')">😰<br>混んでる</button>
    </div>
    <input type="hidden" name="status" id="statusInput" value="<?= htmlspecialchars($current_status) ?>">

    <hr class="divider">

    <div class="section-title">お客さんへのメッセージ（任意）</div>
    <div class="msg-wrap">
      <!-- よく使うメッセージ例 -->
      <div class="msg-examples">
        <span class="msg-example" onclick="setMsg('お菓子の在庫あと少し！')">🍬 お菓子あと少し</span>
        <span class="msg-example" onclick="setMsg('ただいま準備中です。少々お待ちください。')">⏳ 準備中</span>
        <span class="msg-example" onclick="setMsg('完売しました。ありがとうございました！')">✅ 完売</span>
        <span class="msg-example" onclick="setMsg('新商品入荷しました！')">🆕 新商品入荷</span>
        <span class="msg-example" onclick="setMsg('')">🗑 メッセージ削除</span>
      </div>
      <textarea id="msgInput" name="message" placeholder="例：お菓子の在庫あと少し！　など自由に入力（140文字まで）"
        maxlength="140" oninput="updateCount()"><?= htmlspecialchars($current_message) ?></textarea>
      <div class="char-count"><span id="charCount"><?= mb_strlen($current_message) ?></span>/140</div>
    </div>

    <button class="submit-btn" type="submit">更新する</button>
  </form>

  <hr class="divider">
  <div class="back"><a href="../index.html">← お客さん向けページを確認</a></div>
</div>

<script>
  function selectStatus(val) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
  }
  function setMsg(text) {
    const ta = document.getElementById('msgInput');
    ta.value = text;
    updateCount();
  }
  function updateCount() {
    const len = document.getElementById('msgInput').value.length;
    document.getElementById('charCount').textContent = len;
  }
</script>

</body>
</html>

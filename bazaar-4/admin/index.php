<?php
session_start();

// ===== ログイン情報 =====
// パスワードはここで管理します。変更する場合はPWの値を書き換えてください。
$accounts = [
  'basukebu'     => ['name' => 'バスケ部',       'password' => 'wBm448ZS'],
  'sakkaabu'     => ['name' => 'サッカー部',     'password' => '9EtoYigh'],
  'taiikukan'    => ['name' => '体育館',         'password' => 'KPo6fklu'],
  'kagakubu'     => ['name' => '科学部',         'password' => 'bpcS56Dg'],
  'tetsudoclub'  => ['name' => '鉄道研究同好会', 'password' => '2DTLArMD'],
  'sofutenisubu' => ['name' => 'ソフトテニス部', 'password' => '5kGVu6TI'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = trim($_POST['shop_id'] ?? '');
  $pw = trim($_POST['password'] ?? '');

  if (isset($accounts[$id]) && $accounts[$id]['password'] === $pw) {
    $_SESSION['shop_id']   = $id;
    $_SESSION['shop_name'] = $accounts[$id]['name'];
    header('Location: dashboard.php');
    exit;
  } else {
    $error = 'IDまたはパスワードが正しくありません。';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者ログイン｜東星学園バザー</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
      background: #f5f7fa; min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 1rem;
    }
    .card {
      background: #fff; border-radius: 12px; padding: 2.5rem 2rem;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      width: 100%; max-width: 380px;
    }
    .logo {
      text-align: center; margin-bottom: 1.75rem;
    }
    .logo-badge {
      display: inline-block;
      background: #1a3a5c; color: #fff;
      font-size: 11px; letter-spacing: 0.1em;
      padding: 4px 12px; border-radius: 20px; margin-bottom: 0.5rem;
    }
    .logo h1 { font-size: 18px; color: #1a3a5c; font-weight: bold; }
    .logo p  { font-size: 12px; color: #888; margin-top: 4px; }
    label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; }
    input {
      width: 100%; padding: 0.65rem 0.85rem;
      border: 1px solid #dde5ef; border-radius: 6px;
      font-size: 15px; margin-bottom: 1rem;
      transition: border-color 0.2s;
    }
    input:focus { outline: none; border-color: #1a3a5c; }
    .btn {
      width: 100%; padding: 0.75rem;
      background: #1a3a5c; color: #fff;
      border: none; border-radius: 6px;
      font-size: 15px; font-weight: bold; cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover { background: #254f7a; }
    .error {
      background: #ffebee; color: #c62828;
      border-radius: 6px; padding: 0.65rem 0.9rem;
      font-size: 13px; margin-bottom: 1rem;
    }
    .back { text-align: center; margin-top: 1.25rem; font-size: 13px; }
    .back a { color: #1a3a5c; text-decoration: underline; }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">
      <div class="logo-badge">ADMIN</div>
      <h1>東星学園 バザー</h1>
      <p>お店スタッフ用ログイン</p>
    </div>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="shop_id">店舗ID</label>
      <input type="text" id="shop_id" name="shop_id" placeholder="例：kagakubu" autocomplete="username" required>

      <label for="password">パスワード</label>
      <input type="password" id="password" name="password" placeholder="8文字のパスワード" autocomplete="current-password" required>

      <button class="btn" type="submit">ログイン</button>
    </form>

    <div class="back"><a href="../index.html">← お客さん向けページへ</a></div>
  </div>
</body>
</html>

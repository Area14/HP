<?php
// 文字化け対策
mb_language("Japanese");
mb_internal_encoding("UTF-8");

$message = "";

// POSTリクエストかどうか確認
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームデータの受け取りとエスケープ
    $companyName = filter_input(INPUT_POST, 'companyName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '未入力';
    $contactName = filter_input(INPUT_POST, 'contactName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contactEmail = filter_input(INPUT_POST, 'contactEmail', FILTER_SANITIZE_EMAIL);
    $contactType = filter_input(INPUT_POST, 'contactType', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contactMessage = filter_input(INPUT_POST, 'contactMessage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // 必須チェック
    if (empty($contactName) || empty($contactEmail) || empty($contactType) || empty($contactMessage)) {
        $message = "必須項目が正しく入力されていません。";
    }
    elseif (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "有効なメールアドレスをご入力ください。";
    }
    else {
        // 送信先メールアドレス（自社の受信元）
        // ※実際には自社の受信メールアドレスに変更してください
        $to = "info@blueocean-trading.co.jp";

        // 件名
        $subject = "【WEBサイトからのお問い合わせ】" . $contactType;

        // 本文の組み立て
        $body = "WEBサイトのフォームからお問い合わせがありました。\n\n";
        $body .= "■ 貴社名／店舗名\n" . $companyName . "\n\n";
        $body .= "■ お名前\n" . $contactName . " 様\n\n";
        $body .= "■ メールアドレス\n" . $contactEmail . "\n\n";
        $body .= "■ お問い合わせ種別\n" . $contactType . "\n\n";
        $body .= "■ お問い合わせ内容\n" . $contactMessage . "\n";

        // 返信用ヘッダーの設定
        $headers = "From: " . $to . "\r\n";
        $headers .= "Reply-To: " . $contactEmail . "\r\n";

        // メール送信の実行
        if (mb_send_mail($to, $subject, $body, $headers)) {
            $message = "お問い合わせを受け付けました。\n担当者からのご連絡をお待ちください。";
        }
        else {
            $message = "メールの送信に失敗しました。\nご不便をおかけしますが、時間をおいて再度お試しください。";
        }
    }
}
else {
    // 直接アクセスされた場合
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>送信完了｜株式会社Blue Ocean</title>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@400;500;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8fbfd;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'M PLUS Rounded 1c', sans-serif;
        }
        .result-box {
            background: #fff;
            padding: 50px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 123, 181, 0.08);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .result-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .result-message {
            font-size: 1.1rem;
            color: #333;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .back-btn {
            display: inline-block;
            background: #007bb5;
            color: #fff;
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }
        .back-btn:hover {
            background: #005a88;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 181, 0.3);
        }
    </style>
</head>
<body>
    <div class="result-box">
        <div class="result-icon">📩</div>
        <p class="result-message"><?php echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')); ?></p>
        <a href="index.html" class="back-btn">トップページへ戻る</a>
    </div>
</body>
</html>

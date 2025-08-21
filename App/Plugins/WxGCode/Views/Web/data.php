<?php
// 检查是否有 data 参数
if (isset($_POST['data'])) {
    $scanResult = htmlspecialchars($_POST['data']);
} else {
    $scanResult = '未获取到扫描结果';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>扫描结果</title>
    <style>
        body {font-family: Arial, sans-serif; display: flex; flex-direction: column; height: 100vh; margin: 0; background-color: #f4f4f4; } 
        .result-container {background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; width: 90%; max-width: 400px; margin: auto; cursor: pointer; margin-top: 200px; } 
        .back-button-container {position: fixed; bottom: 0; width: 100%; text-align: center; padding: 20px 0; background-color: #007BFF; color: white; cursor: pointer; transition: background-color 0.3s ease; } .back-button-container:hover {background-color: #0056b3; } 
        .copy-toast {position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px 20px; border-radius: 4px; opacity: 0; transition: opacity 0.3s ease; } 
        .result-container p:nth-child(2) {word-wrap: break-word; word-break: break-all; white-space: pre-wrap; }
    </style>
</head>

<body>
    <div class="result-container" onclick="copyResult()">
        <p id="scan-result">扫描结果:</p>
        <p> <?php echo $scanResult; ?></p>
    </div>
    <div class="back-button-container" onclick="history.back()">
        扫一扫
    </div>
    <div id="copy-toast" class="copy-toast">复制成功</div>

    <script>
        function copyResult() {
            const resultElement = document.querySelector('.result-container p:nth-child(2)');
            const textToCopy = resultElement.textContent;
            const textArea = document.createElement('textarea');
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            const toast = document.getElementById('copy-toast');
            toast.style.opacity = 1;
            setTimeout(() => {
                toast.style.opacity = 0;
            }, 2000);
        }
    </script>
</body>

</html>    
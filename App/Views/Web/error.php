<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>操作提示</title>
    <!-- 引入Font Awesome图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .error-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .error-container:hover {
            transform: translateY(-5px);
        }

        .error-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .error-icon {
            font-size: 50px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            animation: pulse 2s infinite;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .error-subtitle {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .error-body {
            padding: 30px 20px;
            text-align: center;
        }

        .error-message {
            font-size: 18px;
            color: #4a4a4a;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 0 20px;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 30px;
            border: none;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-refresh {
            background-color: #4285f4;
            color: white;
        }

        .btn-refresh:hover {
            background-color: #3367d6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
        }

        .btn-back {
            background-color: #f1f3f4;
            color: #202124;
        }

        .btn-back:hover {
            background-color: #e8eaed;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-home {
            background-color: #34a853;
            color: white;
        }

        .btn-home:hover {
            background-color: #2d8643;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 168, 83, 0.3);
        }

        .footer-note {
            margin-top: 30px;
            color: #868686;
            font-size: 14px;
            opacity: 0.8;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @media (max-width: 480px) {
            .error-header {
                padding: 20px 15px;
            }
            
            .error-icon {
                font-size: 40px;
            }
            
            .error-title {
                font-size: 20px;
            }
            
            .error-body {
                padding: 20px 15px;
            }
            
            .error-message {
                font-size: 16px;
                margin-bottom: 20px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 10px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 class="error-title">操作提示</h2>
            <p class="error-subtitle">很抱歉，出现了一些问题</p>
        </div>
        <div class="error-body">
            <div class="error-message">
                <?php echo htmlspecialchars($error); // 显示错误信息 ?>
            </div>
            <div class="error-actions">
                <button class="btn btn-refresh" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> 刷新页面
                </button>
                <button class="btn btn-back" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> 返回上一页
                </button>
                <button class="btn btn-home" onclick="window.location.href='/'">
                    <i class="fas fa-home"></i> 首页
                </button>
            </div>
            <p class="footer-note">如有疑问，请联系系统管理员</p>
        </div>
    </div>
</body>
</html>

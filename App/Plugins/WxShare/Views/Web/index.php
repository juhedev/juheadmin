<?php
$title = htmlspecialchars($share['share_title']);
$desc = htmlspecialchars($share['share_desc']);
$image = htmlspecialchars($share['share_img']);
$link = htmlspecialchars($share['share_link']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>微信卡片分享预览</title>
    <script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
    <style>
        /* 基础样式重置 */
        * {margin: 0; padding: 0; box-sizing: border-box; } 
        body {font-family: "PingFang SC", "Helvetica Neue", Helvetica, Arial, sans-serif; padding: 15px; background-color: #f7f7f7; color: #333; position: relative; min-height: 100vh; } 
        h1 {text-align: center; margin: 20px 0 25px; font-weight: 500; font-size: 18px; color: #333; } 
        .status-info {text-align: center; color: #666; font-size: 15px; padding: 10px; margin: 0 auto 20px; line-height: 1.5; } 
        .blog-list-container {max-width: 640px; margin: 0 auto; }
        .blog-card {display: flex; max-width: 500px; margin: 0 auto; flex-direction: row; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 15px; transition: transform 0.2s, box-shadow 0.2s; height: 120px;} 
        .blog-card:hover {transform: translateY(-2px); box-shadow: 0 3px 8px rgba(0,0,0,0.12); } 
        .blog-image {width: 120px;  height: 100%; flex-shrink: 0; object-fit: cover; } 
        .blog-content {flex: 1;padding: 8px 18px; display: flex; flex-direction: column; justify-content: center; } 
        .blog-title {font-size: 18px; color: #333; line-height: 1.5; margin-bottom: 5px; max-height: 48px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; } 
        .blog-desc {font-size: 15px; color: #666;  overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; } 
        .blog-meta {display: table; justify-content: space-between; align-items: center;margin:0 auto; } 
        .blog-link {font-size: 12px; color: #999; }
        .share-guide {position: fixed; top: -25px;right: 0px; z-index: 999; pointer-events: none; } 
        .share-guide img {width: 120px;height: auto; } 
        @media screen and (max-width: 375px) {.blog-card {height: 100px; } .blog-image {width: 110px; } .blog-content {padding: 8px 15px; } .blog-title {font-size: 15px; margin-bottom: 3px; } .blog-desc {font-size: 12px;  } .share-guide img {width: 100px;} }
    </style>
</head>
<body>
    <!-- 分享引导GIF动画 -->
    <div class="share-guide">
        <!-- 使用指向右上角的引导动画GIF -->
        <img src="https://yanxuan.nosdn.127.net/c1bf3641f8cc21cc05a65bc978cf819b.gif" alt="点击分享引导">
    </div>

    <h1>卡片分享预览</h1>
    <div class="blog-list-container">
        <div class="blog-card">
            <img src="<?= $image ?>" class="blog-image" alt="分享图片">
            <div class="blog-content">
                <div class="blog-title"><?= $title ?></div>
                <div class="blog-desc"><?= $desc ?></div>
            </div>
        </div>
        <div class="blog-meta">
            <div class="blog-link">跳转到：<a target="_blank" href="<?= $link ?>" ><?= $link ?></a></div>
        </div>
    </div>

    <div class="status-info">
        提示：点击右上角菜单选择分享
    </div>

    <script>
        // 从后端获取签名配置
        const signPackage = {
            appId: "<?php echo $signPackage['appId']?>",
            timestamp: "<?php echo $signPackage['timestamp']?>",
            nonceStr: "<?php echo $signPackage['nonceStr']?>",
            signature: "<?php echo $signPackage['signature']?>",
            url: "<?php echo $signPackage['url']?>"
        };

        // 配置微信JS-SDK
        wx.config({
            debug: false,
            appId: signPackage.appId,
            timestamp: signPackage.timestamp,
            nonceStr: signPackage.nonceStr,
            signature: signPackage.signature,
            jsApiList: [
                'updateAppMessageShareData',
                'updateTimelineShareData',
                'onMenuShareAppMessage',
                'onMenuShareTimeline'
            ]
        });

        // 生成带参数的分享链接
        function getShareLink(baseUrl) {
            const param = '?rep';
            if (baseUrl && baseUrl.includes('?')) {
                return baseUrl + '&' + param.substring(1);
            }
            return (baseUrl || window.location.href) + param;
        }

        // 分享配置参数
        const shareConfig = {
            title: '<?= $title ?>',
            desc: '<?= $desc ?>',
            link: getShareLink(signPackage.url),
            imgUrl: '<?= $image ?: 'https://picsum.photos/400/300' ?>'
        };

        // JS-SDK初始化成功回调
        wx.ready(function() {
            // 新接口配置
            wx.updateAppMessageShareData({
                title: shareConfig.title,
                desc: shareConfig.desc,
                link: shareConfig.link,
                imgUrl: shareConfig.imgUrl,
                success: function() {
                    console.log('分享给朋友配置成功');
                }
            });

            wx.updateTimelineShareData({
                title: shareConfig.title,
                link: shareConfig.link,
                imgUrl: shareConfig.imgUrl,
                success: function() {
                    console.log('分享到朋友圈配置成功');
                }
            });

            // 兼容旧版本接口
            if (wx.onMenuShareAppMessage) {
                wx.onMenuShareAppMessage(shareConfig);
            }
            if (wx.onMenuShareTimeline) {
                wx.onMenuShareTimeline({
                    title: shareConfig.title,
                    link: shareConfig.link,
                    imgUrl: shareConfig.imgUrl
                });
            }
        });

        // JS-SDK配置失败回调
        wx.error(function(res) {
            console.error('微信JS-SDK配置失败:', res.errMsg);
        });
    </script>
</body>
</html>
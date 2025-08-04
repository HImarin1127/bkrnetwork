<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>批量產生公司網域網址與QRCODE生成器</title>
    <link rel="stylesheet" href="<?php echo isset(
        $baseUrl) ? $baseUrl : '/bkrnetwork'; ?>/assets/css/styles.css">
    <!-- 移除 Tailwind 與自訂按鈕 style -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        textarea, input[type="text"], select {
            border-radius: 16px !important;
            padding: 1.1rem 1.3rem !important;
            font-size: 1.08rem !important;
            border: 1.5px solid #bbb !important;
            box-shadow: 0 2px 12px rgba(200,16,46,0.06);
            transition: border 0.2s, box-shadow 0.2s;
            background: #fff;
        }
        textarea:focus, input[type="text"]:focus, select:focus {
            border: 2px solid #C8102E !important;
            box-shadow: 0 4px 18px rgba(200,16,46,0.12);
            outline: none;
            background: #fff;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="container mx-auto p-4 sm:p-6 lg:p-8 max-w-4xl">
        <header class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">批量網址與 QR Code 生成器</h1>
            <p class="mt-2 text-slate-600">輸入關鍵字，快速產生公司網域網址、QR Code 及 .htaccess 規則。</p>
        </header>

        <main>
            <!-- Step 1: Generate URLs -->
            <div class="step-card">
                <h2 class="text-xl font-semibold text-slate-900 border-b pb-3 mb-4">Step 1: 輸入關鍵字並生成公司網域網址</h2>
                <textarea id="keywordInput" class="w-full h-32 p-3 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-400 transition" placeholder="輸入關鍵字，每行一筆"></textarea>
                <div class="flex items-center gap-4 mt-4">
                    <button onclick="generateUrls()" class="btn btn-primary">產生公司網域網址</button>
                    <button onclick="clearAll()" class="btn btn-secondary">清除全部</button>
                </div>
                <div class="mt-4">
                    <label class="font-medium text-slate-700">生成結果:</label>
                    <pre id="result" class="w-full mt-2 p-3 bg-slate-100 rounded-md text-sm whitespace-pre-wrap break-all min-h-[100px]"></pre>
                </div>
            </div>

            <!-- Step 2: Generate QR Codes -->
            <div class="step-card">
                <h2 class="text-xl font-semibold text-slate-900 border-b pb-3 mb-4">Step 2: 選擇網址生成 QRCODE</h2>
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                    <div>
                        <label for="sizeSelect" class="font-medium text-slate-700">選擇圖片大小：</label>
                        <select id="sizeSelect" class="mt-1 sm:mt-0 p-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-slate-400 transition">
                            <option value="256">小 (256x256)</option>
                            <option value="512">中 (512x512)</option>
                            <option value="1024">大 (1024x1024)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-4 mt-4 sm:mt-0">
                        <button onclick="generateQRCodes()" class="btn btn-primary">生成QR碼</button>
                        <button onclick="downloadAllQRCodes()" class="btn btn-secondary">全部下載</button>
                        <button onclick="clearQRCodeContainer()" class="btn btn-secondary">清除</button>
                    </div>
                </div>
                <div id="qrcodeContainer" class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
            </div>

            <!-- Step 3: Generate .htaccess -->
            
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script>
        let generatedUrls = '';

        // 驗證關鍵字
        function validateKeyword(keyword) {
            const regex = /^[\u4e00-\u9fa5a-zA-Z0-9-_]+$/;
            return regex.test(keyword);
        }

        // 修改：從伺服器獲取已存在的關鍵字列表，並解析新格式
        async function fetchExistingKeywords() {
            try {
                // 添加時間戳以避免瀏覽器快取舊的 txt 檔案
                const response = await fetch('urldata.txt?t=' + new Date().getTime());
                if (!response.ok) {
                    console.warn('urldata.txt not found. Skipping duplicate check.');
                    return [];
                }
                const text = await response.text();
                const lines = text.split('\n');
                const keywords = [];
                const regex = /^\s*Redirect\s+\/(\S+)/;

                lines.forEach(line => {
                    const match = line.match(regex);
                    if (match && match[1]) {
                        keywords.push(match[1]);
                    }
                });
                return keywords;
            } catch (error) {
                console.error('Error fetching or parsing urldata.txt:', error);
                return [];
            }
        }

        // 整合重複檢查功能 (此函數無須變動)
        async function generateUrls() {
            const input = document.getElementById('keywordInput').value.trim();
            if (!input) {
                alert('請輸入關鍵字');
                return;
            }

            const existingKeywords = await fetchExistingKeywords();
            const existingKeywordsSet = new Set(existingKeywords);

            const keywords = input.split('\n').filter(kw => kw.trim() !== '');
            let result = '';
            let hasInvalidKeyword = false;
            let duplicateKeywords = [];

            keywords.forEach(keyword => {
                const trimmedKeyword = keyword.trim();
                if (existingKeywordsSet.has(trimmedKeyword)) {
                    duplicateKeywords.push(trimmedKeyword);
                    return;
                }
                
                if (validateKeyword(trimmedKeyword)) {
                    const url = `http://qrcode.bookrep.com.tw/${trimmedKeyword}`;
                    result += `${url}\n`;
                } else {
                    alert(`無效的關鍵字: ${trimmedKeyword}`);
                    hasInvalidKeyword = true;
                }
            });
            
            if (duplicateKeywords.length > 0) {
                alert(`關鍵字重複: ${duplicateKeywords.join(', ')}`);
            }

            if (!hasInvalidKeyword && result) {
                generatedUrls = result.trim();
                document.getElementById('result').textContent = generatedUrls;
            } else if (!result && !hasInvalidKeyword && duplicateKeywords.length === keywords.length) {
                document.getElementById('result').textContent = '';
                generatedUrls = '';
            }
        }

        // 清除所有輸入與結果
        function clearAll() {
            document.getElementById('keywordInput').value = '';
            document.getElementById('result').textContent = '';
            generatedUrls = '';
            clearQRCodeContainer();
            clearHtaccessContent();
        }

        // 產生 QR 碼
        function generateQRCodes() {
            if (!generatedUrls) {
                alert('請先在 Step 1 產生公司網域網址');
                return;
            }
            const urls = generatedUrls.split('\n').filter(url => url.trim() !== '');
            const container = document.getElementById('qrcodeContainer');
            const size = parseInt(document.getElementById('sizeSelect').value, 10);
            container.innerHTML = ''; 

            urls.forEach(url => {
                const trimmedUrl = url.trim();
                if (trimmedUrl) {
                    const qrcodeItem = document.createElement('div');
                    qrcodeItem.className = 'flex flex-col items-center text-center p-2 bg-white rounded-lg shadow';
                    
                    const qrcodeCanvasContainer = document.createElement('div');
                    qrcodeCanvasContainer.setAttribute('data-url', trimmedUrl);
                    
                    const urlText = document.createElement('p');
                    urlText.className = 'mt-2 text-xs text-slate-600 break-all';
                    urlText.textContent = trimmedUrl;

                    qrcodeItem.appendChild(qrcodeCanvasContainer);
                    qrcodeItem.appendChild(urlText);
                    container.appendChild(qrcodeItem);

                    new QRCode(qrcodeCanvasContainer, {
                        text: trimmedUrl,
                        width: size,
                        height: size,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            });
        }
        
        // 下載所有 QR 碼
        function downloadAllQRCodes() {
            const zip = new JSZip();
            const container = document.getElementById('qrcodeContainer');
            const qrcodeItems = container.querySelectorAll('div[data-url]');

            if (qrcodeItems.length === 0) {
                alert('請先生成QR碼');
                return;
            }

            qrcodeItems.forEach(item => {
                const canvas = item.querySelector('canvas');
                const url = item.getAttribute('data-url');
                const keyword = url.substring(url.lastIndexOf('/') + 1) || 'qrcode';
                const imgData = canvas.toDataURL('image/png');
                zip.file(`${keyword}.png`, imgData.substr(imgData.indexOf(',') + 1), { base64: true });
            });

            zip.generateAsync({ type: 'blob' }).then(function(content) {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(content);
                link.download = 'qrcodes.zip';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }

        // 清除 QR 碼容器
        function clearQRCodeContainer() {
            document.getElementById('qrcodeContainer').innerHTML = '';
        }

            </script>
</body>
</html> 
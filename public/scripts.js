// 切換登入/註冊表單顯示的函式
function toggleAuth(showRegister) {
    const loginSection = document.getElementById('login');
    const registerSection = document.getElementById('register');
    if (showRegister) {
        loginSection.classList.remove('active');
        registerSection.classList.add('active');
    } else {
        registerSection.classList.remove('active');
        loginSection.classList.add('active');
    }
}

// 等待 DOM 加載後再綁定事件
document.addEventListener('DOMContentLoaded', function() {
    // CSV 匯入頁面的拖拽上傳區域與按鈕
    const importArea = document.querySelector('.import-area');
    const fileInput = document.querySelector('.file-input');
    const uploadBtn = document.querySelector('.upload-btn');

    if (uploadBtn && fileInput) {
        // 點擊「選擇檔案」按鈕時觸發檔案選擇
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });
        // 當使用者選擇檔案後，更新按鈕文字並變更顏色
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                uploadBtn.textContent = `已選擇: ${fileName}`;
                uploadBtn.style.background = '#10b981';
            }
        });
    }

    if (importArea && fileInput && uploadBtn) {
        // 拖曳檔案進入區域時的樣式變化
        importArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            importArea.classList.add('dragover');
        });
        importArea.addEventListener('dragleave', () => {
            importArea.classList.remove('dragover');
        });
        // 拖放檔案到區域時的處理
        importArea.addEventListener('drop', (e) => {
            e.preventDefault();
            importArea.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                // 將檔案加入表單的檔案輸入欄位
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                // 更新按鈕文字顯示所選檔案名稱並變色
                uploadBtn.textContent = `已拖入: ${file.name}`;
                uploadBtn.style.background = '#10b981';
            }
        });
    }
});
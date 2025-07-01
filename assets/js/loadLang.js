// Lấy số hiệu phiên bản mới nhất của ngôn ngữ
async function fetchLangMTime(langCode) {
    let formData = new FormData();

    if (langCode) {
        formData.append("keys[]", langCode);
    } else {
        let cachedLang;
        try {
            cachedLang = JSON.parse(localStorage.getItem('language')) || {};
        } catch {
            localStorage.removeItem('language');
            cachedLang = {};
        }
        let keys = Object.keys(cachedLang);
        keys.forEach(key => { formData.append("keys[]", key) });
    }

    if ([...formData.entries()].length === 0) {
        console.warn("No language keys provided for version check.")
        return null;
    }

    let response = await fetch("php/class/language/fetchLangVersion.php", {
        method: "POST",
        body: formData
    });
    let responeData = await response.text();
    try {
        let data = JSON.parse(responeData);

        if (data.ok) {
            return data.mtime;
        } else {
            console.error(data.message);
        }
    } catch (error) {
        console.error(error);
        console.error(responeData);
        return null;
    }
}

async function syncLangCacheWithServer() {
    let mtime = await fetchLangMTime();
    if (mtime === null) {
        return;
    }

    let cachedLang;
    try {
        cachedLang = JSON.parse(localStorage.getItem('language')) || {};
    } catch {
        cachedLang = {};
        localStorage.removeItem('language');
    }

    for (const [key, version] of Object.entries(mtime)) {
        if (version !== null && cachedLang[key]) {
            if (cachedLang[key]['version'] !== version) {
                cachedLang[key] = await loadLang(key);
            }
        }
    }

    localStorage.setItem("language", JSON.stringify(cachedLang));
}

// Lấy ngôn ngữ người dùng từ server (database)
async function getUserLang() {
    let response = await fetch("php/class/language/getUserLang.php");
    let responeData = await response.text();
    try {
        let data = JSON.parse(responeData);
        if (data.ok) {
            return data.userLang;
        } else {
            console.warn(data.message);
            return null;
        }
    } catch (error) {
        console.error(error);
        console.error(responeData);
        return null;
    }
}

// Lưu ngôn ngữ người dùng vào server (database)
async function setUserLang(userLang) {
    localStorage.setItem('userLang', userLang);
    const formData = new FormData();
    formData.append("userLang", userLang);
    try {
        let response = await fetch("php/class/language/setUserLang.php", {
            method: 'POST',
            body: formData
        });

        let responseData = await response.text();
        let data;
        try {
            data = JSON.parse(responseData);
        } catch (error) {
            console.error(responseData);
            console.error(error);
            return;
        }

        if (!data.ok) {
            console.warn(data.message);
            return;
        }
    } catch (error) {
        console.error(error);
    }
}

// Lấy ngôn ngữ mặc định từ trình duyệt
function getBrowserLang() {
    let lang = navigator.language || navigator.userLanguage || "en";
    return lang.replace('_', '-').split('-')[0].toLowerCase();
}

// Tải dữ liệu ngôn ngữ từ YAML
async function loadLang(langCode) {
    let langURL = `configs/languages/${langCode}.yml`;
    try {
        let res = await fetch(langURL);
        if (!res.ok) {
            throw new Error("File does not exist: " + langURL);
        }

        let langText = await res.text();
        if (langText.includes("<!DOCTYPE html>") || langText.includes("<html")) {
            throw new Error("File does not exist: " + langURL);
        }

        let langData = jsyaml.load(langText);
        let langVersion = await fetchLangMTime(langCode);

        let lang = {
            version: langVersion[langCode],
            data: langData
        }

        return lang;
    } catch (error) {
        console.error(error);
        return null;
    }
}

// Cache dữ liệu ngôn ngữ vào local storage
function cacheLanguage(langCode, lang) {
    let cachedLang;
    try {
        cachedLang = JSON.parse(localStorage.getItem('language')) || {};
    } catch {
        cachedLang = {};
        localStorage.removeItem('language');
    }
    cachedLang[langCode] = lang;

    localStorage.setItem('language', JSON.stringify(cachedLang));
}

// Lấy dữ liệu ngôn ngữ, fallback và lưu cache
async function getLang(langCode) {
    let cachedLang;
    try {
        cachedLang = JSON.parse(localStorage.getItem('language')) || {};
    } catch {
        cachedLang = {};
        localStorage.removeItem('language');
    }

    if (!langCode) {
        langCode = await getUserLang();
        if (!langCode) {
            langCode = localStorage.getItem('userLang');

            if (!langCode) {
                langCode = getBrowserLang();
            }
        }
    }
    
    let lang;
    if(!(lang = cachedLang[langCode])) {
        if(!(lang = await loadLang(langCode))) {
            if(!(lang = cachedLang['en'])) {
                if(!(lang = await loadLang('en'))) {
                    return null;
                } else {
                    langCode = 'en';
                    cacheLanguage(langCode,lang);
                }
            } else {
                langCode = 'en';
            }
        } else {
            cacheLanguage(langCode,lang);
        }
    }
        
    return {
        code: langCode,
        data: lang.data
    }
}

// Lấy giá trị từ object theo đường dẫn key dạng "a.b.c"
function getNestedValue(object, path) {
    return path.split(".").reduce(((accumulator, key) => accumulator?.[key]), object);
}

// Áp dụng dữ liệu ngôn ngữ vào các phần tử có thuộc tính data-lang
async function applyLang(langData) {
    if (langData.code) {
        await setUserLang(langData.code);
    }

    document.querySelectorAll('[data-lang]').forEach(el => {
        const key = el.getAttribute('data-lang');
        const value = getNestedValue(langData.data || {}, key) || "...";
        if (el instanceof HTMLInputElement) {
            el.placeholder = value;
        } else {
            el.innerText = value;
        }
    });
}

// Đổi ngôn ngữ theo lựa chọn người dùng
async function changeLang(langCode) {
    const langData = await getLang(langCode);
    applyLang(langData);
}



window.onload = async () => {
    // Đồng bộ dữ liệu ngôn ngữ mới 
    await syncLangCacheWithServer();

    // Lấy và áp dụng ngôn ngữ ngay khi trang vừa load
    const langData = await getLang();
    applyLang(langData);
}
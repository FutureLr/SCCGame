// Lấy phiên bản mới của tất cả file ngôn ngữ
async function loadAllLangVersion() {
    let keys = JSON.parse(localStorage.getItem('language'));
    let formData = new FormData();
    keys.forEach(key => {formData.append("keys[]",key)});

    let response = await fetch("php/class/language/checkLangVersion.php");
    let responeData = await response.text();
    try {
        let data = JSON.parse(responeData);

        // mai lam tiep 
    } catch(error) {
        console.log(error);
        console.log(responeData);
    }
}

window.addEventListener("load",loadAllLangVersion);

// Lấy ngôn ngữ người dùng từ server (database)
async function getUserLang() {
    let response = await fetch("php/class/language/getUserLang.php");
    let responeData = await response.text();
    try {
        let data = JSON.parse(responeData);
        if (data.ok) {
            return data.userLang;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error(error);
        console.error(responeData);
        return null;
    }
}

// Lưu ngôn ngữ người dùng vào server (database)
async function setUserLang(userLang) {
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

        if(!data.ok) {
            throw new Error(data.message);
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

// Tải dữ liệu ngôn ngữ từ YAML (có cache và fallback)
async function loadLang(lang) {
    let userLang;
    if (lang) {
        userLang = lang;
        await setUserLang(userLang);
    } else {
        if (!(userLang = await getUserLang())) {
            if (!(userLang = JSON.parse(localStorage.getItem("userLang")))) {
                userLang = getBrowserLang();
                localStorage.setItem("userLang",userLang);
            }
        }
    }

    let cachedLang = {};
    try {
        cachedLang = JSON.parse(localStorage.getItem("language") || "{}");
    } catch (e) {
        console.warn("Language data in LocalStorage is corrupted. Resetting it now.");
        localStorage.removeItem("language");
    }

    let cachedLangData = cachedLang[userLang] ? cachedLang[userLang]["data"] : null;
    if (cachedLangData) {
        return cachedLangData;
    }
    
    let langURL = `configs/languages/${userLang}.yml`;
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
        if (!cachedLang[userLang]) {
            cachedLang[userLang] = {};
        }
        cachedLang[userLang]["data"]=langData;
        localStorage.setItem("language", JSON.stringify(cachedLang));
        return langData;
    } catch (error) {
        console.error(error);

        if (cachedLang["en"] ? cachedLang["en"]["data"] : null) {
            return cachedLang["en"]["data"];
        }
        const fallbackURL = "configs/languages/en.yml";
        let res = await fetch(fallbackURL);
        if (!res.ok) {
            throw new Error("File does not exist: " + fallbackURL);
        }
        let langText = await res.text();
        if (langText.includes("<!DOCTYPE html>") || langText.includes("<html")) {
            throw new Error("File does not exist: " + fallbackURL);
        }
        
        let langData = jsyaml.load(langText);
        cachedLang["en"]["data"] = langData;
        localStorage.setItem("language", JSON.stringify(cachedLang));
        return langData;
    }
}

// Lấy giá trị từ object theo đường dẫn key dạng "a.b.c"
function getNestedValue(object, path) {
    return path.split(".").reduce(((accumulator, key) => accumulator?.[key]), object);
}

// Áp dụng dữ liệu ngôn ngữ vào các phần tử có thuộc tính data-lang
function applyLang(langData) {
    document.querySelectorAll('[data-lang]').forEach(el => {
        const key = el.getAttribute('data-lang'); 
        const value = getNestedValue(langData, key) || "..."; 
        if (el instanceof HTMLInputElement) {
            el.placeholder = value;
        } else {
            el.innerText = value;
        }
    });
}

// Tải và áp dụng ngôn ngữ ngay khi trang vừa load
loadLang().then(langData => applyLang(langData));

// Đổi ngôn ngữ theo lựa chọn người dùng
function changeLang(lang) {
    loadLang(lang).then(langData => applyLang(langData));
}

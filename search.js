function search(keyword) {
    if (window.location.href.includes("?")) {
        let params = window.location.href.split("?")[1].split("&");
        let keys = [];
        let values = [];
        for (let index = 0; index < params.length; index++) {
            const param = params[index].split("=");
            keys.push(param[0]);
            values.push(param[1]);
        }
        let href_result = window.location.href.split("?")[0] + "?";
        let done = false;
        for (let index = 0; index < keys.length; index++) {
            const key = keys[index];
            if (key != "s") {
                const value = values[index];

                if (done) {
                    href_result += "&";
                }
                else {
                    done = true;
                }

                href_result += key + "=" + value;
            }
        }
        if (done) {
            href_result += "&";
        }
        href_result += "s=" + keyword;
        console.log(href_result);
        window.location.href = href_result;
    }
    else {
        window.location.href = window.location.href + "?s=" + keyword;
    }
}

const search_bar = document.getElementById("search-input");
const search_button = document.getElementById("search-button");

window.addEventListener("keypress", event => {
    console.log(event.key);
    if (event.key == "Enter" && document.activeElement == search_bar) {
        search(search_bar.value);
    }
});

window.addEventListener("click", event => {
    if (event.target == search_bar || event.target == search_button) {
        search_bar.parentElement.classList.add("active");
    }
    else {
        search_bar.parentElement.classList.remove("active");
    }
});
document.addEventListener("DOMContentLoaded", () => {
  window.onpopstate = () => location.reload();
  document.body.querySelectorAll("script:not([static])").forEach((tag) => tag.setAttribute("static", ""));
  _app.core.run();
  _app.alert(currentAlert);
  console.log("⚡ENERGIZE⚡");
});

const _page = {};

const _app = {
  REGISTRED: {},
  WORKING: false,
  core: {
    run() {
      Object.keys(_app.REGISTRED).forEach((querySelector) =>
        document.body.querySelectorAll(querySelector).forEach((element) => {
          _app.REGISTRED[querySelector](element);
          element.setAttribute("static", "");
        })
      );
      document.body.querySelectorAll("script:not([static])").forEach((tag) => {
        eval(tag.innerHTML);
        tag.setAttribute("static", "");
      });
      _app.aside();
    },
    register(querySelector, action) {
      _app.REGISTRED[querySelector] = action;
    },
    request(url = null, method = "get", data = {}, header = {}, useWorking = true) {
      return new Promise(function (resolve, reject) {
        if (useWorking && _app.WORKING) return reject("working");

        if (useWorking) _app.WORKING = true;
        document.body.classList.add("__working__");

        var xhr = new XMLHttpRequest();

        url = url ?? window.location.href;

        xhr.open(method, url, true);
        xhr.setRequestHeader("Request-Partial", true);

        for (let key in header) xhr.setRequestHeader(key, header[key]);

        xhr.responseType = "json";

        xhr.onload = () => {
          if (useWorking) _app.WORKING = false;
          document.body.classList.remove("__working__");

          let resp = xhr.response;

          if (!resp.info || !resp.info.mx)
            resp = {
              info: {
                mx: false,
                error: xhr.status > 399,
                staus: xhr.status,
              },
              data: resp,
            };

          if (resp.info.alert ?? false) _app.alert(resp.info.alert);

          if (resp.info.location ?? false) {
            _app.go(resp.info.location, true);
            return reject("redirect");
          }

          return resolve(resp);
        };

        xhr.send(data);
      });
    },
    loadScript(src) {
      return new Promise(function (resolve, reject) {
        if (document.head.querySelectorAll(`script[src="${src}"]`).length) return resolve();
        let script = document.createElement("script");
        script.async = "true";
        script.src = src;
        script.onload = () => resolve();
        document.head.appendChild(script);
      });
    },
  },
  update: {
    content(content) {
      let element = document.getElementById("CONTENT");
      element.innerHTML = content;
      _app.core.run();
    },
    layout(content, state) {
      let element = document.getElementById("LAYOUT");
      element.innerHTML = content;
      element.dataset.state = state;
      _app.core.run();
    },
    location(url) {
      if (url != window.location) history.pushState({ urlPath: url }, null, url);
    },
    head(head) {
      document.title = head.title;
      document.head.querySelector('meta[name="description"]').setAttribute("content", head.description);
      document.head.querySelector('link[rel="icon"]').setAttribute("href", head.favicon);
    },
  },
  vue: {
    mountApp(component, elementId) {
      _app.core
        .loadScript("/assets/third/vue.js")
        .then(() => Vue.createApp(component).mount(elementId))
        .catch((e) => console.error("impossible to load [vue.js]", e));
    },
    encapsulate(value) {
      return JSON.stringify(value);
    },
    decapsulate(value) {
      return JSON.parse(value);
    },
    importField(fieldId) {
      return _app.vue.decapsulate(document.getElementById(fieldId).value);
    },
    exportField(value, fieldId) {
      document.getElementById(fieldId).value = _app.vue.encapsulate(value);
    },
    submitForm(formId) {
      document.getElementById(formId).requestSubmit();
    },
  },
  go(url, force = false) {
    if (!force && url == window.location) return;
    if (new URL(url).hostname != new URL(window.location).hostname) return _app.redirect(url);
    let state = document.getElementById("LAYOUT").dataset.state;
    _app.core
      .request(url, "get", {}, { "Request-State": state })
      .then((resp) => {
        if (!resp.info.mx) return _app.redirect(url);
        if (resp.info.error) return;
        _app.update.head(resp.data.head);
        _app.update.location(url);

        if (resp.data.state == state) {
          _app.update.content(resp.data.content);
        } else {
          _app.update.layout(resp.data.content, resp.data.state);
        }

        window.scrollTo(0, 0);
        return;
      })
      .catch(() => null);
  },
  fragment(url, target, mode = 0) {
    _app.core
      .request(url, "get", {}, { "Request-Fragment": true }, false)
      .then((resp) => {
        if (mode) {
          target.insertAdjacentHTML(mode == 1 ? "beforeend" : "afterbegin", resp.data.content);
        } else {
          target.innerHTML = resp.data.content;
        }
        _app.core.run();
      })
      .catch(() => null);
  },
  api(url, method, data) {
    return _app.core.request(url, method, data, { "Request-Api": true }, false);
  },
  redirect(url) {
    window.location.href = url;
    return false;
  },
  aside(asideId = null) {
    let aside = document.getElementById(asideId);

    let mode = aside && !aside.classList.contains("__show__");

    document.querySelectorAll(".__show__").forEach((element) => element.classList.remove("__show__"));

    if (mode) {
      document.body.classList.add("__show__");
      aside.classList.add("__show__");
    }
  },
  alert(listAlert) {
    let div = document.getElementById("ALERT");
    listAlert.forEach((item) => {
      let title = item[0] ?? "";
      let content = item[1] ?? "";
      let type = item[2] ?? "";
      let svg = {
        neutral: `[#ICON:alert/neutral]`,
        success: `[#ICON:alert/success]`,
        error: `[#ICON:alert/error]`,
      }[type];
      let alert = `<div class="${type}">${svg}<span>${title}</span><span>${content}</span></div>`;
      div.insertAdjacentHTML("beforeend", alert);
    });
    div.querySelectorAll("div:not([static])").forEach((e) => {
      e.setAttribute("static", "");
      setTimeout(function () {
        e.remove();
      }, 5000);
    });
  },
  uid() {
    return "_" + Date.now().toString(36) + Math.random().toString(36).substr(2);
  },
  copy(copyText, alertText = null) {
    document.body.classList.add("__copying__");
    let copy = document.getElementById("COPY");
    copy.value = copyText;
    copy.select();
    copy.setSelectionRange(0, 99999);
    document.execCommand("copy");
    document.body.classList.remove("__copying__");
    if (alertText) _app.alert([[alertText, "", "success"]]);
  },
  debounce(func, wait) {
    let timer = null;
    return () => {
      clearTimeout(timer);
      timer = setTimeout(func, wait);
    };
  },
  submit(form, appentData = {}) {
    let showmessage = form.querySelector(".form-alert");

    if (form.dataset.alert) showmessage = document.getElementById(form.dataset.alert);

    if (showmessage) showmessage.innerHTML = "";

    form.querySelectorAll("[data-input].error").forEach((label) => {
      label.classList.remove("error");
    });

    let url = form.action;
    let state = document.getElementById("LAYOUT").dataset.state;
    let header = { "Request-State": state };
    let data = new FormData(form);

    for (const [key, value] of Object.entries(appentData)) {
      data.append(key, value);
    }

    form.querySelectorAll("input[type=file]").forEach((input) => {
      for (var i = 0; i < input.files.length; i++) {
        if (input.getAttribute("name")) {
          data.append(input.getAttribute("name") + "[]", input.files[i]);
        }
      }
    });

    _app.core
      .request(url, form.getAttribute("method") ?? "post", data, header)
      .then((resp) => {
        if (resp.info.error && form.dataset.error) return eval(form.dataset.error)(resp);

        if (!resp.info.error && form.dataset.success) return eval(form.dataset.success)(resp);

        if (resp.data) {
          _app.update.head(resp.data.head);
          _app.update.location(url);
          if (resp.data.state == state) _app.update.content(resp.data.content);
          else _app.update.layout(resp.data.content, resp.data.state);
          window.scrollTo(0, 0);
          return;
        }

        if (resp.info.error && resp.info.field) {
          let label = form.querySelector(`[data-input=${resp.info.field}]`);
          if (label) label.classList.add("error");
        }

        if (showmessage) {
          let spanClass = `sts_` + (resp.info.error ? "erro" : "success");
          let message = resp.info.message ?? (resp.info.error ? "erro" : "ok");
          let description = resp.info.description ?? "";
          if (description) description = `<span>${description}</span>`;
          showmessage.innerHTML = `<span class='${spanClass}'><span>${message}</span>${description}</span>`;
        }
      })
      .catch(() => null);
  },
};

// [#VIEW:./register/dinamicLink.js]
// [#VIEW:./register/form.js]
// [#VIEW:./register/fragment.js]
// [#VIEW:./register/currentLink.js]
// [#VIEW:./register/openMenu.js]

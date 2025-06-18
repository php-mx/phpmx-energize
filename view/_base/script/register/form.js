_app.core.register("form:not([static])", (element) => {
  element.addEventListener("submit", async (ev) => {
    ev.preventDefault();
    _app.submit(element);
  });
});

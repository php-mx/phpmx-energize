energize.core.register("form:not([static])", (element) => {
  element.addEventListener("submit", async (ev) => {
    ev.preventDefault();
    energize.submit(element);
  });
});

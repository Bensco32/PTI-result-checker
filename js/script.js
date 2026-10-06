// PTI Result Checker — UI interactions only.
// All student/result data is loaded from the server (PHP + PostgreSQL).
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".main-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", () => {
      const open = nav.classList.toggle("nav-open");
      toggle.setAttribute("aria-expanded", String(open));
    });
  }

  // Confirmation dialogs for actions marked data-confirm
  document.querySelectorAll("[data-confirm]").forEach((el) => {
    el.addEventListener("click", (event) => {
      if (!window.confirm(el.getAttribute("data-confirm"))) {
        event.preventDefault();
      }
    });
  });

  // Loading indicator on form submit
  document.querySelectorAll("form[data-loading]").forEach((form) => {
    form.addEventListener("submit", () => {
      const button = form.querySelector('button[type="submit"]');
      if (button && !button.disabled) {
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = "Please wait…";
        button.disabled = true;
      }
    });
  });
});

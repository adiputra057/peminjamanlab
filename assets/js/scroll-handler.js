// Scroll ke elemen dengan ID dari query string (?page=...)
window.onload = function () {
  const params = new URLSearchParams(window.location.search);
  const target = params.get("page");
  if (target) {
    const el = document.getElementById(target);
    if (el) {
      el.scrollIntoView({ behavior: "smooth" });
    }
  }
};

// Smooth scroll untuk pagination
document.addEventListener('DOMContentLoaded', function () {
  const paginationLinks = document.querySelectorAll('.pagination .page-link');
  
  paginationLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      const statusSection = document.getElementById('status');
      if (statusSection) {
        setTimeout(() => {
          statusSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
    });
  });
});

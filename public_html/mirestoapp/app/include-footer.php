<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div
            class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body mb-2 mb-md-0">
            © <script>
                document.write(new Date().getFullYear());
            </script>, Sistema para clínicas
            </div>
            <div class="d-none d-lg-inline-block">
            <a href="#" class="footer-link d-none d-sm-inline-block">Soporte</a>
            </div>
        </div>
    </div>
</footer>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Obtener la URL actual y extraer el nombre del archivo
    const currentPath = window.location.pathname.split("/").pop();

    
    // Buscar todos los enlaces del menú
    const menuLinks = document.querySelectorAll(".menu-link");

    menuLinks.forEach(link => {
      // Extraer el nombre del archivo del atributo href del enlace
      const linkPath = link.getAttribute("href").split("/").pop();

      // Comparar el archivo del enlace con el de la URL actual
      if (linkPath === currentPath) {
        // Agregar la clase "active" al enlace actual
        const menuItem = link.closest(".menu-item");
        if (menuItem) {
          menuItem.classList.add("active");
        }

        // Si es un submenú, abrir también su menú padre
        const parentMenu = menuItem?.closest(".menu-item.menu-toggle");
        if (parentMenu) {
          parentMenu.classList.add("open", "active");
        }
      }
    });
  });
</script>
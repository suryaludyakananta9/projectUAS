/* =========================================================
   validasi.js
   1. Semua input wajib diisi
   2. Konfirmasi sebelum hapus
   3. Validasi upload file (ekstensi & ukuran)
   ========================================================= */

const EKSTENSI_VALID = ["jpg", "jpeg", "png"];
const MAX_UKURAN_BYTE = 2 * 1024 * 1024;

document.addEventListener("DOMContentLoaded", function () {
  aktifkanValidasiForm();
  aktifkanKonfirmasiHapus();
  aktifkanValidasiUpload();
});

/* ---------------------------------------------------------
   1. Validasi input wajib diisi sebelum form dikirim
   --------------------------------------------------------- */
function aktifkanValidasiForm() {
  document.querySelectorAll("form.form-validasi").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      let valid = true;

      form.querySelectorAll("[required]").forEach(function (input) {
        const field = input.closest(".field") || input.parentElement;
        const errorEl = field ? field.querySelector(".error-text") : null;
        const kosong = input.type === "file"
          ? input.files.length === 0
          : input.value.trim() === "";
        const pesan = input.type === "file" ? "Pilih file terlebih dahulu." : "Kolom ini wajib diisi.";

        if (kosong) {
          valid = false;
          if (field) field.classList.add("has-error");
          if (errorEl) errorEl.textContent = pesan;
        } else {
          if (field) field.classList.remove("has-error");
        }
      });

      if (!valid) {
        e.preventDefault();
        const firstError = form.querySelector(".has-error");
        if (firstError) firstError.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    });

    form.querySelectorAll("[required]").forEach(function (input) {
      input.addEventListener("input", function () {
        const field = input.closest(".field") || input.parentElement;
        if (field) field.classList.remove("has-error");
      });
    });
  });
}

/* ---------------------------------------------------------
   2. Konfirmasi sebelum hapus data
   --------------------------------------------------------- */
function aktifkanKonfirmasiHapus() {
  document.querySelectorAll(".btn-hapus").forEach(function (link) {
    link.addEventListener("click", function (e) {
      const nama = link.getAttribute("data-nama") || "data ini";

      const jumlahProduk = link.getAttribute("data-jumlah");
      if (jumlahProduk !== null && parseInt(jumlahProduk, 10) > 0) {
        e.preventDefault();
        alert(
          "Kategori \"" + nama + "\" tidak bisa dihapus karena masih dipakai oleh " +
          jumlahProduk + " produk.\n\nPindahkan atau hapus produknya terlebih dahulu."
        );
        return;
      }

      const ok = confirm("Yakin ingin menghapus \"" + nama + "\"? Data yang dihapus tidak dapat dikembalikan.");
      if (!ok) {
        e.preventDefault();
      }
    });
  });
}

/* ---------------------------------------------------------
   3. Validasi upload file: ekstensi & ukuran maksimal
   --------------------------------------------------------- */
function aktifkanValidasiUpload() {
  document.querySelectorAll("input[type=file].validasi-gambar").forEach(function (input) {
    input.addEventListener("change", function () {
      const field = input.closest(".field") || input.parentElement;
      const errorEl = field ? field.querySelector(".error-text") : null;
      const file = input.files[0];

      if (!file) return;

      const ekstensi = file.name.split(".").pop().toLowerCase();
      const validEkstensi = EKSTENSI_VALID.includes(ekstensi);
      const validUkuran = file.size <= MAX_UKURAN_BYTE;

      if (!validEkstensi) {
        tampilkanErrorUpload(field, errorEl, "Format file harus JPG, JPEG, atau PNG.");
        input.value = "";
        return;
      }

      if (!validUkuran) {
        tampilkanErrorUpload(field, errorEl, "Ukuran file maksimal 2MB.");
        input.value = "";
        return;
      }

      if (field) field.classList.remove("has-error");
    });
  });
}

function tampilkanErrorUpload(field, errorEl, pesan) {
  if (field) field.classList.add("has-error");
  if (errorEl) errorEl.textContent = pesan;
  alert(pesan);
}

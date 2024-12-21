<!DOCTYPE html>
<html lang="en">
  <?php
// Memuat file head.php untuk elemen HTML head seperti meta tag, CSS, dan title halaman
require "layout/head.php";

// Memuat file conn.php untuk membuat koneksi ke database
require "include/conn.php";
?>

  <body>
    <div id="app">
      <?php 
      // Memuat file sidebar.php yang berisi elemen navigasi samping
      require "layout/sidebar.php"; 
      ?>
      <div id="main">
        <header class="mb-3">
          <!-- Tombol burger untuk menampilkan/meminimalkan sidebar pada layar kecil -->
          <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
          </a>
        </header>
        <div class="page-heading">
          <h3>Matrik</h3> <!-- Judul halaman -->
        </div>
        <div class="page-content">
          <section class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <!-- Judul bagian yang menjelaskan isi tabel -->
                  <h4 class="card-title">Matriks Keputusan (X) &amp; Ternormalisasi (R)</h4>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <!-- Penjelasan tentang metode normalisasi yang digunakan -->
                    <p class="card-text">Melakukan perhitungan normalisasi untuk mendapatkan matriks nilai ternormalisasi (R), dengan ketentuan :
Untuk normalisai nilai, jika faktor/attribute kriteria bertipe cost maka digunakan rumusan:
Rij = ( min{Xij} / Xij)
sedangkan jika faktor/attribute kriteria bertipe benefit maka digunakan rumusan:
Rij = ( Xij/max{Xij} )</p>
                  </div>
                  <!-- Tombol untuk menampilkan modal form input nilai alternatif -->
                  <button type="button" class="btn btn-outline-success btn-sm m-2" data-bs-toggle="modal"
                                        data-bs-target="#inlineForm">
                                        Isi Nilai Alternatif
                                    </button>
                  <div class="table-responsive">
                  <!-- Tabel untuk menampilkan matriks keputusan -->
                  <table class="table table-striped mb-0">
    <caption>
        Matrik Keputusan(X) <!-- Judul tabel -->
    </caption>
    <tr>
        <th rowspan='2'>Alternatif</th>
        <th colspan='6'>Kriteria</th>
    </tr>
    <tr>
        <th>C1</th>
        <th>C2</th>
        <th>C3</th>
        <th>C4</th>
        <th colspan="2">C5</th>
    </tr>
    <?php
// Query untuk mengambil nilai matriks keputusan dari database
$sql = "SELECT
          a.id_alternative,
          b.name,
          SUM(IF(a.id_criteria=1,a.value,0)) AS C1,
          SUM(IF(a.id_criteria=2,a.value,0)) AS C2,
          SUM(IF(a.id_criteria=3,a.value,0)) AS C3,
          SUM(IF(a.id_criteria=4,a.value,0)) AS C4,
          SUM(IF(a.id_criteria=5,a.value,0)) AS C5
        FROM
          saw_evaluations a
          JOIN saw_alternatives b USING(id_alternative)
        GROUP BY a.id_alternative
        ORDER BY a.id_alternative";

// Menjalankan query
$result = $db->query($sql);

// Array untuk menyimpan nilai matriks keputusan berdasarkan kriteria
$X = array(1 => array(), 2 => array(), 3 => array(), 4 => array(), 5 => array());

// Menampilkan data hasil query dalam tabel
while ($row = $result->fetch_object()) {
    array_push($X[1], round($row->C1, 2));
    array_push($X[2], round($row->C2, 2));
    array_push($X[3], round($row->C3, 2));
    array_push($X[4], round($row->C4, 2));
    array_push($X[5], round($row->C5, 2));
    echo "<tr class='center'>
            <th>A<sub>{$row->id_alternative}</sub> {$row->name}</th>
            <td>" . round($row->C1, 2) . "</td>
            <td>" . round($row->C2, 2) . "</td>
            <td>" . round($row->C3, 2) . "</td>
            <td>" . round($row->C4, 2) . "</td>
            <td>" . round($row->C5, 2) . "</td>
            <td>
            <a href='keputusan-hapus.php?id={$row->id_alternative}' class='btn btn-danger btn-sm'>Hapus</a>
            </td>
          </tr>\n";
}
// Membebaskan memori hasil query
$result->free();
?>
</table>

<!-- Tabel untuk menampilkan matriks ternormalisasi -->
<table class="table table-striped mb-0">
    <caption>
        Matrik Ternormalisasi (R) <!-- Judul tabel -->
    </caption>
    <tr>
        <th rowspan='2'>Alternatif</th>
        <th colspan='5'>Kriteria</th>
    </tr>
    <tr>
        <th>C1</th>
        <th>C2</th>
        <th>C3</th>
        <th>C4</th>
        <th>C5</th>
    </tr>
    <?php
// Query untuk menghitung nilai matriks ternormalisasi
$sql = "SELECT
          a.id_alternative,
          SUM(
            IF(
              a.id_criteria=1,
              IF(
                b.attribute='benefit',
                a.value/" . max($X[1]) . ",
                " . min($X[1]) . "/a.value)
              ,0)
              ) AS C1,
          SUM(
            IF(
              a.id_criteria=2,
              IF(
                b.attribute='benefit',
                a.value/" . max($X[2]) . ",
                " . min($X[2]) . "/a.value)
               ,0)
             ) AS C2,
          SUM(
            IF(
              a.id_criteria=3,
              IF(
                b.attribute='benefit',
                a.value/" . max($X[3]) . ",
                " . min($X[3]) . "/a.value)
               ,0)
             ) AS C3,
          SUM(
            IF(
              a.id_criteria=4,
              IF(
                b.attribute='benefit',
                a.value/" . max($X[4]) . ",
                " . min($X[4]) . "/a.value)
               ,0)
             ) AS C4,
          SUM(
            IF(
              a.id_criteria=5,
              IF(
                b.attribute='benefit',
                a.value/" . max($X[5]) . ",
                " . min($X[5]) . "/a.value)
               ,0)
             ) AS C5
        FROM
          saw_evaluations a
          JOIN saw_criterias b USING(id_criteria)
        GROUP BY a.id_alternative
        ORDER BY a.id_alternative";

// Menjalankan query dan menyimpan hasil normalisasi
$result = $db->query($sql);
$R = array();

// Menampilkan data hasil normalisasi dalam tabel
while ($row = $result->fetch_object()) {
    $R[$row->id_alternative] = array($row->C1, $row->C2, $row->C3, $row->C4, $row->C5);
    echo "<tr class='center'>
            <th>A{$row->id_alternative}</th>
            <td>" . round($row->C1, 2) . "</td>
            <td>" . round($row->C2, 2) . "</td>
            <td>" . round($row->C3, 2) . "</td>
            <td>" . round($row->C4, 2) . "</td>
            <td>" . round($row->C5, 2) . "</td>
          </tr>\n";
}
?>
</table>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
        <?php 
        // Memuat file footer.php untuk elemen footer
        require "layout/footer.php"; 
        ?>
      </div>
    </div>

    <!-- Modal untuk menambahkan nilai alternatif -->
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Isi Nilai Kandidat </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <!-- Form untuk mengisi nilai kandidat -->
                    <form action="matrik-simpan.php" method="POST">
                        ...
                    </form>
                </div>
            </div>
        </div>

    <?php 
    // Memuat file js.php untuk elemen script JavaScript
    require "layout/js.php"; 
    ?>
  </body>

</html>

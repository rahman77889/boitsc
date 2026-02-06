<style>
  #autocomplete_preview {
    display: none;
    position: absolute;
    top: 2rem;
    left: 3rem;
    background-color: white;
    width: 15rem;
  }

  #autocomplete_preview a:hover {
    background-color: #ddd;
    font-weight: bold;
  }

  #autocomplete_preview a,
  #autocomplete_preview b {
    display: block;
    padding: 0.5rem 1rem;
    cursor: pointer;
    text-decoration: none;
    color: #333333;
  }
</style>
<script>
  $(function () {
    $('#txtSearchMenu').keyup(function () {
      const key = $(this).val().toString();
      $('#autocomplete_preview').html('');

      if (key.length >= 2) {
        var links_filtered = [];

        var links = $('nav a');

        for (var l = 0; l < links.length; l++) {
          var ln = links.eq(l).text().toString().trim();
          var ll = links.eq(l).attr('href');

          if (ln && ll && ln.toLowerCase().indexOf(key) >= 0) {
            if (ll.indexOf('#') >= 0) {
              links_filtered.push(`<b>${ln}</b>`);
            } else {
              links_filtered.push(`<a href="${ll}">${ln}</a>`);
            }
          }
        }

        $('#autocomplete_preview').html(links_filtered.join(''));

        $('#autocomplete_preview').show();
      } else {
        $('#autocomplete_preview').hide();
      }
    });
  })
</script>

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center"
    style="    background: #ffffff !important;">
    <a class="navbar-brand brand-logo" href="../../index.html">
      <img src="<?= base_url('template/'); ?>img/telkomcel.png" alt="logo" /> </a>
    <a class="navbar-brand brand-logo-mini" href="../../index.html">
      <img src="<?= base_url('template/'); ?>assets/images/logo-mini.svg" alt="logo" /> </a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block">
        <div class="helpdesk" style="color:#867979">Centro Informação<span class="version"
            style="background-color:#6d6060;padding:4px;">V.1</span></div>
      </li>
      <li style="position:relative">
        <input type="search" class="form-control" id="txtSearchMenu" placeholder="🔎 Search menu"
          style="width:15rem;margin-left:3rem;">
        <div id="autocomplete_preview">
        </div>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="<?= base_url('upload/' . $this->session->userdata('photo')); ?>"
            alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="<?= base_url('upload/' . $this->session->userdata('photo')); ?>"
              alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold"><?= $this->session->userdata('fullName'); ?></p>
            <p class="font-weight-light text-muted mb-0"><?= $this->session->userdata('privilageName'); ?></p>
          </div>
          <!-- <a class="dropdown-item">My Profile </a> -->
          <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">Edit Profile </a>


          <!-- <a class="dropdown-item">FAQ<i class="dropdown-item-icon ti-help-alt"></i></a> -->
          <a href="javascript:void()" class="logoutLink dropdown-item">Sign Out<i
              class="dropdown-item-icon ti-power-off"></i></a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
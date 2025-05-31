<div class="breadcrumbs">
  <div class="container">
    <div class="row">
      <div class="col s12">
        <div class="crud_header">
          <?=$this->crud_tabla_header($data_crud);?>
        </div>
      </div>
    </div>
  </div>
</div>

<?=$this->crud_tabla_totales($data_crud);?>

<div id="filters" class="fadeTop"></div>

<div class="row">
  <div class="col s12">
    <div class="container">
      <section class="section">
        <div class="responsive-table">
          <table class="table" id="crud_adnsy" data-crud="<?=$crud_adnsy;?>" data-unik="<?=wp_create_nonce($crud_adnsy);?>" style="width: 100%">
            <thead>
              <tr>
                <?=$this->crud_tabla($data_crud);?>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</div>

<script type="text/javascript">
  <?=$this->crud_js_tabla($data_crud);?>
</script>
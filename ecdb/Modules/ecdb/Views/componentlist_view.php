<?php 
  global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/ecdb/ecdb.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>

<style>
input[type="text"] {
     width: 88%; 
}
</style>

<br>
<div class="container">

    <ul class="nav nav-pills">
      <li class="active">
        <a href="<?php echo $path; ?>ecdb/component/list">My components</a>
      </li>
      <li><a href="<?php echo $path; ?>ecdb/component/add" >Add component</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/shopping" >Shopping list</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>

    <div id="localheading"><h3><?php echo _('My components'); ?></h3></div>
    
    <div id="table"></div>

    <div id="no-components" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('No components in database'); ?></h4>
    </div>
    
    <p>Total stock excluding VAT: <b>£<span id="totalstock_exvat"></span></b></p>
    <p>Total stock including 20% VAT: <b>£<span id="totalstock_incvat"></span></b></p>
</div>

    <div id="addstock-modal" class="modal hide">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Add stock</h3>
    </div>
    <div class="modal-body">
    <p>Add order quantity to exisiting stock of: <b><span id="modal-component-name" ></span></b></p>
    <input id="stocktoadd" type="text" />
    </div>
    <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <a id="addstock-save" href="#" class="btn btn-primary">Save changes</a>
    </div>
    </div>

<script>


  var path = "<?php echo $path; ?>";

  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'id':{'title':"<?php echo _('ID'); ?>", 'type':"fixed"},
    'name':{'title':"<?php echo _('Name'); ?>", 'type':"text"},
    'category':{'title':"<?php echo _('Category'); ?>", 'type':"text"},
    'partcode':{'title':"<?php echo _('Part Code'); ?>", 'type':"text"},
    'datasheet':{'title':"<?php echo _('Datasheet'); ?>", 'type':"iconlink_value", 'icon':'icon-book'},
        
    'manufacturer':{'title':"<?php echo _('Manufacturer'); ?>", 'type':"text"},
    'package':{'title':"<?php echo _('Package'); ?>", 'type':"text"},
    'smd':{'title':"<?php echo _('SMD'); ?>", 'type':"text"},
    'price':{'title':"<?php echo _('Price (£)'); ?>", 'type':"text"},
    'quantity':{'title':"<?php echo _('Quantity'); ?>", 'type':"text"},
    'add-action':{'title':"<?php echo _(''); ?>", 'type':"iconlink", 'icon':"icon-plus", 'link':"#"},
    
    //'order_quantity':{'title':"<?php echo _('Order quantity'); ?>", 'type':"text"},

    // Actions
    'edit-action':{'title':'', 'type':"edit"},
    'view-action':{'title':'', 'type':"iconlink", 'link':path+"ecdb/component/edit?id="}

  }

  table.groupby = 'category';
  table.deletedata = false;

  table.data = ecdb.component.list();
  table.draw();
  
  if (table.data.length != 0) {
    $("#no-components").hide();    
    $("#localheading").show();      
  } else {
    $("#no-components").show();
    $("#localheading").hide();
  }

  $("#table").bind("onSave", function(e,id,fields_to_update){
    ecdb.component.set(id,fields_to_update); 
    
    var totalstock = 0;
    for (z in table.data) totalstock += 1*table.data[z].quantity * 1*table.data[z].price
    $("#totalstock_exvat").html(totalstock.toFixed(0));
    $("#totalstock_incvat").html((totalstock*1.2).toFixed(0));
  });

  var totalstock = 0;
  for (z in table.data) totalstock += 1*table.data[z].quantity * 1*table.data[z].price
  
  $("#totalstock_exvat").html(totalstock.toFixed(0));
  $("#totalstock_incvat").html((totalstock*1.2).toFixed(0));
  
  var modal_uid = 0;
  
  $("#table").on('click', 'td[field=add-action]', function() {
    $("#stocktoadd").val(0);
    $('#addstock-modal').modal('show');
    
    modal_uid = $(this).parent().attr('uid');
    $("#modal-component-name").html(table.data[modal_uid].name);
  });
  
  $("#addstock-save").click(function()
  {
    var stocktoadd = parseInt($("#stocktoadd").val());
    var currentquantity = table.data[modal_uid].quantity * 1;
    
    table.data[modal_uid].quantity = currentquantity + stocktoadd;
    var id = table.data[modal_uid].id;
    
    ecdb.component.set(id,{'quantity':table.data[modal_uid].quantity});
    
    table.draw();
    $('#addstock-modal').modal('hide');
  });

</script>

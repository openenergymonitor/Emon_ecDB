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
      <li><a href="<?php echo $path; ?>ecdb/component/list">My components</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/add" >Add component</a></li>
      <li class="active"><a href="<?php echo $path; ?>ecdb/component/shopping" >Shopping list</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>

    <div id="localheading"><h3><?php echo _('Shopping list'); ?></h3></div>
 
    <div id="table"></div>

    <div id="no-components" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('No components in database'); ?></h4>
    </div>
</div>

<script>


  var path = "<?php echo $path; ?>";

  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
  
    'name':{'title':"<?php echo _('Name'); ?>", 'type':"text"},
    'manufacturer':{'title':"<?php echo _('Manufacturer'); ?>", 'type':"text"},
    'package':{'title':"<?php echo _('Package'); ?>", 'type':"text"},
    'smd':{'title':"<?php echo _('SMD'); ?>", 'type':"text"},
    'price':{'title':"<?php echo _('Price'); ?>", 'type':"text"},
    'quantity':{'title':"<?php echo _('Quantity'); ?>", 'type':"text"},
    'order_quantity':{'title':"<?php echo _('Quantity to order'); ?>", 'type':"text"},
    
    // Actions
    'edit-action':{'title':'', 'type':"edit"}

  }

  table.groupby = 'tag';
  table.deletedata = false;

  table.data = ecdb.component.list();
  for (z in table.data)
  {
    if (parseInt(table.data[z]['order_quantity'])==0 ) table.data.splice(z,1);
  }
  
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
  });

</script>

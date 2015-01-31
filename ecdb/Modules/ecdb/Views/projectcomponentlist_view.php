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
      <li><a href="<?php echo $path; ?>ecdb/component/shopping" >Shopping list</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>

    <div id="localheading"><h3><?php echo _('Project'); ?>: <span id="projectname"></span></h3></div>

    <div class="input-prepend input-append">
      <span class="add-on">New name: </span>
      <input id="newname" type="text" style="width:200px;" />
      <button id="setname" class="btn" type="button">Update</button>
    </div><br>
    
    <div class="input-prepend input-append">
      <span class="add-on">Add component: </span>
      <select id="component-selector"></select>
      <input id="quantity" type="text" style="width:80px;" />
      <button id="add_component" class="btn" type="button">Add</button>
    </div>
    
    <div id="status" class="alert alert-info"></div>
    
    <div id="table"></div>
   
    <div id="no-components" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('No projects in database'); ?></h4>
    </div>
    
    <p>Total price excluding VAT: <b>£<span id="totalprice_exvat"></span></b></p>
    <p>Total price including 20% VAT: <b>£<span id="totalprice_incvat"></span></b></p>
</div>

<script>

  var project_id = <?php echo $id; ?>;
  
  var path = "<?php echo $path; ?>";
  
  var allcomponents = ecdb.component.list();
  
  allcomponents = allcomponents.sort(function(a,b) {
  return a.name.toUpperCase() > b.name.toUpperCase();
  });
  
  var projectname = ecdb.project.getname(project_id);
  $("#projectname").html(projectname);
  $("#newname").val(projectname);
  
  $("#setname").click(function(){
    var newname = $("#newname").val();
    ecdb.project.setname(project_id,newname);
  
  });
  
  var out = "";
  for (z in allcomponents)
  {
    out += "<option value='"+allcomponents[z].id+"'>"+allcomponents[z].name+"</option>";
  }
  
  $("#component-selector").html(out);
  
  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
  
    'name':{'title':"<?php echo _('Name'); ?>", 'type':"fixed"},
    'category':{'title':"<?php echo _('Category'); ?>", 'type':"fixed"},
    'price':{'title':"<?php echo _('Price (£)'); ?>", 'type':"fixed"},
    'quantity':{'title':"<?php echo _('Quantity'); ?>", 'type':"text"},
    'instock':{'title':"<?php echo _('In Stock'); ?>", 'type':"fixed"},
    'makes':{'title':"<?php echo _('Enough to make'); ?>", 'type':"fixed"},    
    // Actions
    'edit-action':{'title':'', 'type':"edit"},
    'view-action':{'title':'', 'type':"iconlink", 'link':path+"ecdb/component/edit?id="}

  }

  //table.groupby = 'category';
  table.deletedata = false;

  table.data = ecdb.project.componentlist(project_id); 
  
  table.draw();
  
  var bottleneck = {id:0, name:'', makes:Infinity};
  
  for (z in table.data)
  {
    if (table.data[z].makes<bottleneck.makes) {
      bottleneck = {id:table.data[z].id,name:table.data[z].name,makes:table.data[z].makes};
    }
  }
  $("#status").html("<b>Status:</b> With current stock a maximum of <b>"+bottleneck.makes+" units</b> can be produced, the bottleneck is <b>"+bottleneck.name+"</b>");
  
  if (table.data.length != 0) {
    $("#no-components").hide();      
  } else {
    $("#no-components").show();
  }
  
  $("#add_component").click(function(){
    var component_id = $("#component-selector").val();
    var quantity = $("#quantity").val();
    ecdb.project.component(project_id,component_id,quantity);
    table.data = ecdb.project.componentlist(project_id);
    table.draw();
  });
  
  $("#table").bind("onSave", function(e,id,fields_to_update){
  
    console.log(fields_to_update);
    
    if (fields_to_update)
    {
      ecdb.project.component(project_id,id,fields_to_update.quantity);
      table.data = ecdb.project.componentlist(project_id);
      table.draw();
    }
    
    var totalprice = 0;
    for (z in table.data) totalprice += 1*table.data[z].price * table.data[z].quantity;
    ecdb.project.setcostprice(project_id,totalprice);
    $("#totalprice_exvat").html(totalprice.toFixed(2));
    $("#totalprice_incvat").html((totalprice*1.2).toFixed(2));
  });
  
  var totalprice = 0;
  for (z in table.data) totalprice += 1*table.data[z].price * table.data[z].quantity;
  ecdb.project.setcostprice(project_id,totalprice);
  $("#totalprice_exvat").html(totalprice.toFixed(2));
  $("#totalprice_incvat").html((totalprice*1.2).toFixed(2));

</script>

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
      <li class="active"><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>

    <div id="localheading"><h3><?php echo _('My Projects'); ?></h3></div>

    <div class="input-prepend input-append">
      <span class="add-on">New project name: </span>
      <input id="project_name" type="text" style="width:200px" />
      <button id="add_project" class="btn" type="button">Add</button>
    </div>
    
    <div id="table"></div>

    <div id="no-components" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('No projects in database'); ?></h4>
    </div>
</div>

<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo _('WARNING deleting a project is permanent'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo _('Are you sure you want to delete this project?'); ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
    <button id="confirmdelete" class="btn btn-primary"><?php echo _('Delete permanently'); ?></button>
  </div>
</div>

<script>


  var path = "<?php echo $path; ?>";

  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'project_id':{'title':"Id",'type':'fixed'},
    'project_name':{'title':"<?php echo _('Name'); ?>", 'type':"textlink", 'link':path+"ecdb/project/view?id="},
    'project_group':{'title':"Group",'type':'text'},
    'project_costprice':{'title':"Cost Price<br>(Ex VAT)",'type':'fixedprice'},
    'project_sellingprice':{'title':"Selling Price<br>(Ex VAT)",'type':'textprice'},
    'incvat':{'title':"Selling Price<br>(Inc VAT)",'type':'textprice'},    
    'duplicate-action':{'title':"<?php echo _('Duplicate'); ?>", 'type':"iconlink", 'icon': 'icon-random', 'link':path+"ecdb/project/duplicate.json?id="},
    
    'edit-action':{'title':'', 'type':"edit"},
    'delete-action':{'title':'', 'type':"delete"},
    'view-action':{'title':'', 'type':"iconlink", 'link':path+"ecdb/project/view?id="}
  }

  table.groupby = 'project_group';
  table.deletedata = false;

  table.data = ecdb.project.list();
  for (z in table.data) table.data[z]['incvat'] = (table.data[z]['project_sellingprice'] * 1.2);
  table.draw();
  
  if (table.data.length != 0) {
    $("#no-components").hide();    
    $("#localheading").show();      
  } else {
    $("#no-components").show();
    $("#localheading").hide();
  }
  
  $("#add_project").click(function()
  {
  
    var project_name = $("#project_name").val();
    
    if (project_name=='') {
      alert("Please enter project name");
    } else {
      ecdb.project.add(project_name);
    
      table.data = ecdb.project.list();
      for (z in table.data) table.data[z]['incvat'] = (table.data[z]['project_sellingprice'] * 1.2);
      table.draw();
    }
  });
  
  $("#table").bind("onSave", function(e,id,fields_to_update){
    console.log(fields_to_update);
    
    if (fields_to_update['name']) ecdb.project.setname(id,fields_to_update['name']);
    if (fields_to_update['project_group']) ecdb.project.setgroup(id,fields_to_update['project_group']);
    if (fields_to_update['project_sellingprice']) ecdb.project.setsellingprice(id,fields_to_update['project_sellingprice']);
    
    for (z in table.data) table.data[z]['incvat'] = (table.data[z]['project_sellingprice'] * 1.2);
    table.draw();
  });
  
  $("#table").bind("onDelete", function(e,id,row){
    $('#myModal').modal('show');
    $('#myModal').attr('feedid',id);
    $('#myModal').attr('feedrow',row);
  });

  $("#confirmdelete").click(function()
  {
    var id = $('#myModal').attr('feedid');
    var row = $('#myModal').attr('feedrow');
    ecdb.project.remove(id); 
    table.remove(row);
    table.draw();

    $('#myModal').modal('hide');
  });

</script>

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
      <li class="active"><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>

    <div id="localheading"><h3><?php echo _('Production explorer'); ?></h3></div>

    <table class="table">
      <tr><th>Projects</th><th>Produce</th><th>Max in-stock production left</th><th>Bottleneck</th></tr>
      <tbody id="projects"></tbody>
    </table>
    
    <button class="btnproduce btn btn-large btn-primary" style="float:right">Produce</button>
    <br><br>

    <h3>To order:</h3>

    <table class="table">
      <tr><td>Name</td><td>Quantity</td></tr>
      <tbody id="orderlist"></tbody>
    </table>
    
</div>

<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo _('Confirm production'); ?></h3>
  </div>
  <div class="modal-body">
    <div id="productionlist"></div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
    <button id="confirmproduce" class="btn btn-primary"><?php echo _('Go'); ?></button>
  </div>
</div>

<script>


  var path = "<?php echo $path; ?>";
  
  var projects = ecdb.project.list();
  var components = ecdb.component.list();
  
  for (z in projects)
  {
    projects[z].components = ecdb.project.componentlist(projects[z].project_id);
    var bottleneck = {id:0, name:'', makes:Infinity};
    for (i in projects[z].components)
    {
      if (projects[z].components[i].makes<bottleneck.makes) {
        bottleneck = {
          id:projects[z].components[i].id,
          name:projects[z].components[i].name,
          makes:projects[z].components[i].makes
        };
      }
    }
    projects[z].bottleneck = bottleneck;
    console.log(bottleneck.makes);
  }

  var out = "";
  for (z in projects)
  {
    out += "<tr><td>"+projects[z].project_name+"</td><td><input id='produce_"+z+"' class='produce' type='text' style='width:100px' value=0></td><td id='makes_"+z+"'>"+projects[z].bottleneck.makes+"</td><td id='bottleneck_"+z+"'>"+projects[z].bottleneck.name+"</td></tr>";
  
  }
  $("#projects").html(out);
  
  $(".produce").keyup(function(){
    //var z = $(this).attr('z');
    //var project_id = $(this).attr('pid');

    //console.log("Produce "+z+" "+project_id+" "+produce);
    
    var tmp_components = eval(JSON.stringify(components));

    for (z in projects)
    {
      var produce = $("#produce_"+z).val();
      // subtract project components x quantity from stock
      for (i in projects[z].components)
      {
        var component_id = projects[z].components[i].id;
        for (n in tmp_components)
        {
          if (tmp_components[n].id == component_id) {
            tmp_components[n].quantity -= projects[z].components[i].quantity * produce;
          }
        }
      }
    }


    // recalculate makes for all products
    for (z in projects)
    {
      var bottleneck = {id:0, name:'', makes:Infinity};
      for (i in projects[z].components)
      {

        // Search to find id
        var in_stock_quantity = 0;
        for (n in tmp_components)
        {      
          if (tmp_components[n].id == projects[z].components[i].id)
          {
            in_stock_quantity = tmp_components[n].quantity;
          }
        }
        
        // recalculate makes and bottleneck
        projects[z].components[i].makes = in_stock_quantity / projects[z].components[i].quantity;
        if (projects[z].components[i].makes<bottleneck.makes) {
          bottleneck = {
            id:projects[z].components[i].id,
            name:projects[z].components[i].name,
            makes:projects[z].components[i].makes
          };
        }
        
      }
      projects[z].bottleneck = bottleneck;
      //console.log(bottleneck.makes);
      $("#makes_"+z).html(parseInt(projects[z].bottleneck.makes));
      $("#bottleneck_"+z).html(projects[z].bottleneck.name);
    }
    
    var out = "";
    for (n in tmp_components)
    {
      if (tmp_components[n].quantity<0)
      {
        out += "<tr><td>"+tmp_components[n].name+"</td><td>"+(tmp_components[n].quantity*-1)+"</td></tr>";
      }
    }
    
    $("#orderlist").html(out);
    
  });
  
  $(".btnproduce").click(function(){
    $('#myModal').modal('show');
    
    var out = "";
    
    for (z in projects)
    {
      var produce = $("#produce_"+z).val();
      
      if (produce>0) out += "<p><b>"+produce+"x</b> "+projects[z]['project_name']+"</p>";
    }
    $("#productionlist").html(out);
  });
  
  $("#confirmproduce").click(function(){

    console.log("confirm produce");
    for (z in projects)
    {
      var produce = $("#produce_"+z).val() * 1;
      if (produce>0) {
        var project_id = projects[z]['project_id'];
        
        console.log(project_id,produce);
        ecdb.project.produce(project_id,produce);
      }
      $("#produce_"+z).val(0);
    }
    $('#myModal').modal('hide');
  });

</script>

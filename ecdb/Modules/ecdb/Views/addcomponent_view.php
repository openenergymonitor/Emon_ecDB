<?php 
  global $path; 
  
  if (isset($id) && $id>0) $mode = "edit"; else {$mode = "add"; $id = 0;}
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/ecdb/ecdb.js"></script>

<style>

  .labels {
    font-weight:bold;
  } 
  
  /*
    #addtable td:nth-of-type(0) { width:200px; }
    #addtable td:nth-of-type(2) { width:200px; }
  */
</style>

<br>

    <ul class="nav nav-pills">
      <li><a href="<?php echo $path; ?>ecdb/component/list">My components</a></li>
      <li class="active"><a href="<?php echo $path; ?>ecdb/component/add" >Add component</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/shopping" >Shopping list</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
      <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
      <li><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
    </ul>


<div id="localheading"><h3><?php if ($mode=="add") echo "Add"; else echo "Edit"; ?> component </h3></div>

<form id="form">    
<label><b>Comment</b></label>
<textarea name="comment" id="comment" rows="6" style="width:100%"></textarea>

<table class="table" id="addtable" >
  <tr>
    <td><span class="labels">Name</span></td>
    <td><input type="text" name="name" style="width:200px"></td>
    
    <td><span class="labels">Category</span></td>
    <td><input type="text" name="category" style="width:200px"></td>  

    <td><span class="labels">Quantity</span></td>
    <td><input type="text" name="quantity" style="width:50px"></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Manufacturer</span></td>
    <td><input type="text" name="manufacturer" style="width:200px"></td>
    
    <td><span class="labels">Package</span></td>
    <td><input type="text" name="package" style="width:200px"></td>  

    <td><span class="labels">Pins</span></td>
    <td><input type="text" name="pins" style="width:50px"></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Part Code</span></td>
    <td><input type="text" name="partcode" style="width:200px"></td>  
    
    <td><span class="labels">Price ex-VAT</span></td>
    <td><input type="text" name="price" style="width:200px"></td>  

    <td><span class="labels">To order </span></td>
    <td><input type="text" name="order" style="width:50px"></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Location</span></td>
    <td><input type="text" name="location" style="width:200px"></td>
    
    <td><span class="labels">Scrap</span></td>
    <td><input type="text" name="scrap" style="width:200px"></td>  

    <td><span class="labels">Public </span></td>
    <td><input type="text" name="public" style="width:50px"></td>  
  </tr>
  
  <tr>
    <td><span class="labels">SMD</span></td>
    <td><input type="text" name="smd" style="width:200px"></td>
    
    <td><span class="labels">Width</span></td>
    <td><input type="text" name="width" style="width:50px"> mm</td>  

    <td></td><td></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Weight</span></td>
    <td><input type="text" name="weight" style="width:50px"> g</td>
    
    <td><span class="labels">Depth</span</td>
    <td><input type="text" name="depth" style="width:50px"> mm</td>  

    <td></td><td></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Datasheet URL</span></td>
    <td><input type="text" name="datasheet" style="width:200px"></td>
    
    <td><span class="labels">Height</span></td>
    <td><input type="text" name="height" style="width:50px"> mm</td>  

    <td></td><td></td>  
  </tr>
  
  <tr>
    <td><span class="labels">Image URL 1</span></td>
    <td><input type="text" name="image_url_1" style="width:200px"></td>
    
    <td><span class="labels">Image URL 2</span></td>
    <td><input type="text" name="image_url_2" style="width:200px"></td>  

    <td></td><td></td>  
  </tr>
  
</table>
</form>

<button id="add" class="btn btn-primary">Add</button>
<button id="update" class="btn btn-primary" style="display:none">Update</button>
<button id="delete" class="btn btn-warning" style="display:none">Delete (no warning)</button>

<span id="saved" style="color:#888; padding-left:10px"></span>

<script>
  var path = "<?php echo $path; ?>";
  
  var id = <?php echo $id; ?>;
  
  if (id>0) {
    var properties = ecdb.component.get(id);
    for(z in properties) $("input[name="+z+"]").val(properties[z]);
    
    $("#comment").val(properties['comment']);
    $("#add").html("Create duplicate");
    $("#update").show();
    $("#delete").show();
  }

  $("#add").click(function()
  {
    var properties = {};
    var formarray = $("#form").serializeArray();
    

    for (z in formarray) properties[formarray[z].name] = formarray[z].value;
    properties['datasheet'] = encodeURIComponent(properties['datasheet']);
    properties['comment'] = properties['comment'];    
    ecdb.component.add(properties);
    
    $("#add").html("Create duplicate");
    $("#update").show();
    $("#delete").show();
  });
  
  $("#update").click(function()
  {
    var properties = {};
    var formarray = $("#form").serializeArray();
    for (z in formarray) properties[formarray[z].name] = formarray[z].value;
    properties['datasheet'] = encodeURIComponent(properties['datasheet']);
    properties['comment'] = properties['comment'];  
    
    var result = ecdb.component.set(id,properties);
    if (result.success == true) {
      $("#saved").html("Saved");
    } else {
      $("#saved").html("No changes");
    }
  });
  
  $("#delete").click(function()
  {
    ecdb.component.remove(id);
    window.location = path+"ecdb/component/list";
  });
  
  $("input, textarea").keyup(function(){
    $("#saved").html("Changed, click update to save");
  });

</script>

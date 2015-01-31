<?php 
  global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/ecdb/ecdb.js"></script>
<br>
<ul class="nav nav-pills">
  <li><a href="<?php echo $path; ?>ecdb/component/list">My components</a></li>
  <li><a href="<?php echo $path; ?>ecdb/component/add" >Add component</a></li>
  <li><a href="<?php echo $path; ?>ecdb/component/shopping" >Shopping list</a></li>
  <li><a href="<?php echo $path; ?>ecdb/project/list" >Projects</a></li>
  <li><a href="<?php echo $path; ?>ecdb/project/make" >Make!</a></li>
  <li class="active"><a href="<?php echo $path; ?>ecdb/component/log" >Log</a></li>
</ul>
    
<h3>Component Log</h3>

<table class="table">
  <tr><th>Time</th><th>Message</th></tr>
  <tbody id="log"></tbody>
</table>

<script>

  var path = "<?php echo $path; ?>";

  var componentlog = ecdb.component.getlog();

  var out = "";
  for (z in componentlog)
  {

    var date = new Date(componentlog[z].time*1000);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    
    var d = date.getDate();
    var m = months[date.getMonth()];
    var y = date.getFullYear();
    // hours part from the timestamp
    var hours = date.getHours();
    // minutes part from the timestamp
    var minutes = date.getMinutes();
    // seconds part from the timestamp    
    var seconds = date.getSeconds();

    // will display time in 10:30:23 format
    var formattedTime = d+' '+m+' '+y+" "+hours + ':' + minutes + ':' + seconds

    out += "<tr><td>"+formattedTime+"</td><td>"+componentlog[z].message+"</td></tr>";
  }

  $("#log").html(out);

</script>

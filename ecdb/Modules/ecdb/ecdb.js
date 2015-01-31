var ecdb = {

  'component': {

    'list':function()
    {
      var result = {};
      $.ajax({ url: path+"ecdb/component/list.json", dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'get':function()
    {
      var result = {};
      $.ajax({ url: path+"ecdb/component/get.json", data: "id="+id, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'set':function(id, fields)
    {
      var result = {};
      $.ajax({ type:'POST', url: path+"ecdb/component/set.json", data: "id="+id+"&properties="+JSON.stringify(fields), dataType: 'json', async: false, success: function(data){result = data;} });
      return result;
    },
    
    'add':function(properties)
    {
      var result = {};
      $.ajax({ type:'POST', url: path+"ecdb/component/add.json", data: "properties="+JSON.stringify(properties), async: false, success: function(data){} });
      return result;
    },

    'projects':function(component_id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/component/projects.json", data: "id="+component_id, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'remove':function(id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/component/delete.json", data: "id="+id, async: false, success: function(data){} });
      return result;
    },
    
    'getlog':function()
    {
      var result = {};
      $.ajax({ url: path+"ecdb/component/log.json", dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    }
  
  },
  
  'project': {
  
    'add':function(name)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/add.json", data: "name="+name, async: false, success: function(data){} });
      return result;
    },
    
    'list':function()
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/list.json", dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'getname':function(id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/getname.json", data: "id="+id, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'setname':function(id,name)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/setname.json", data: "id="+id+"&name="+name, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'setgroup':function(id,group)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/setgroup.json", data: "id="+id+"&group="+group, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'setcostprice':function(id,costprice)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/setcostprice.json", data: "id="+id+"&costprice="+costprice, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'setsellingprice':function(id,sellingprice)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/setsellingprice.json", data: "id="+id+"&sellingprice="+sellingprice, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'remove':function(id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/delete.json", data: "id="+id, async: false, success: function(data){} });
      return result;
    },
    
    'component':function(id, component_id, component_quantity)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/component.json", data: "id="+id+"&component_id="+component_id+"&component_quantity="+component_quantity, async: false, success: function(data){} });
      return result;
    },
    
    'componentlist':function(project_id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/componentlist.json", data: "id="+project_id, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    },
    
    'duplicate':function(id)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/duplicate.json", data: "id="+id, async: false, success: function(data){} });
      return result;
    },
    
    'produce':function(id,quantity)
    {
      var result = {};
      $.ajax({ url: path+"ecdb/project/produce.json", data: "id="+id+"&quantity="+quantity, dataType: 'json', async: false, success: function(data) {result = data;} });
      return result;
    }
    
  }
}


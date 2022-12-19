<?php
 require_once('server/main.php');
 $name = empty($_GET['name']) ? null : $_GET['name'];
 $del = empty($_GET['del']) ? null : $_GET['del'];
 global $db;
 if($name != null){
 	echo 'Created Successfully';
 	Tw_VendorAddFoodCats($name);
 }else if($del != null){
 	echo 'Delete Successfully';
 	Tw_VendorDeleteFoodCat($del);
 }
 $cats = $db->get(FOOD_CAT);
?>
<?php foreach ($cats as $cat) { ?>
 <div style="display:flex;flex-direction:row">
	 <div><?php echo $cat->name ?></div>
	 <button onclick="deleteCat(<?php echo $cat->id ?>)">Delete</button>
 </div>
<?php } ?>
<input type="text" id="name" />

<input 
  value="Add Category" 
  id="category" 
  type="submit" 
  onclick="onSubmit()"
/>
<script>
	function onSubmit(){
		var name = document.getElementById('name').value;
		var prefix = 'https://clufter.com/category.php?name=';
		if(name.length === 0){
			alert('Please Enter Category Name')
		}else{
			window.location.assign(prefix+name);
		}
	}
	function deleteCat(id) {		
		var prefix = 'https://clufter.com/category.php?del=';
		window.location.assign(prefix+id);
	}	
</script>
---
---
<?php
session_start();
include_once('lib/auth.inc.php');

// if havent log in
if (!($auth_info = auth())) {
	header('Location: login.php', true, 302);
	exit();
}
// if user is not admin
if (!$auth_info[1]) {
	header('Location: index.php', true, 302);
	exit();
}
?>

{% capture content %}
<!--
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
drag and drop:
	https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
	https://codepen.io/prasanjit/pen/NxjZMO
	https://css-tricks.com/examples/DragAndDropFileUploading/
-->

<?php 
$cidOptions = '';
foreach ($cats as $value) {
	$cidOptions .= "<option value=\"$value[CID]\">{htmlspecialchars($value['NAME'])}</option>";
}
?>
<section class="row mx-auto text-center">
	<div class="col-3 px-0 admin-nav">
		<ul class="nav flex-column">
			<li class="nav-item"><a href="admin.php?page=category" class="nav-link text-black py-3">Categories</a></li>
			<li class="nav-item"><a href="admin.php?page=product" class="nav-link text-black py-3">Products</a></li>
		</ul>
	</div>
	<div class="col px-4">
		<?php
		$dstpage = $_GET["page"] ?? "";
		if ($dstpage == "category") {
		?>
			<h3 class="my-4 text-start">Categories</h3>
			<table class="db-table mb-4">
				<tr>
					<th></th>
					<th><button id="cat_add_button"><i class="bi bi-database-add"></i></button></th>
					<th>CID</th>
					<th>Name</th>
				</tr>
				<?php 
				foreach ($cats as $value) {
					$catRow = "<tr class=\"cat-row\">
					<td><button><i class=\"bi bi-trash-fill\"></i></button></td>
					<td><button><i class=\"bi bi-pencil-fill\"></i></button></td>
					<td>{$value['CID']}</td>
					<td>{htmlspecialchars($value['NAME'])}</td>
					</tr>";
					echo $catRow;
				}
				?>
			</table>
			<fieldset class="px-4 text-start w-75 mb-4 d-none">
				<legend>Add New Category</legend>
				<form method="POST" id="cat_insert" action="admin_process.php?action=cat_insert">
					<input id="cat_add_name" class="m-3" type="text" name="name" required pattern="^[\w\- ]+$" placeholder="Enter Category Name" />
					<input type="submit" value="Add" />
					<input type="hidden" name="nonce" value="<?= csrf_getNonce('cat_insert') ?>" />
				</form>
			</fieldset>
			<fieldset class="px-4 text-start w-75 mb-4 d-none">
				<legend>Edit Category</legend>
				<form method="POST" id="cat_edit" action="admin_process.php?action=cat_edit">
					<select id="cat_edit_cid" class="mb-3 d-block" name="cid"><?= $cidOptions ?></select>
					<input id="cat_edit_name" class="mb-3" type="text" name="name" required pattern="^[\w\- ]+$" placeholder="Enter Category Name" />
					<input type="submit" value="Edit" />
					<input type="hidden" name="nonce" value="<?= csrf_getNonce('cat_edit') ?>" />
				</form>
			</fieldset>
			<form method="POST" id="cat_del" class="d-none" action="admin_process.php?action=cat_delete">	
				<input type="hidden" name="cid" value="0" required />
				<input type="hidden" name="nonce" value="<?= csrf_getNonce('cat_delete') ?>" />
			</form>
		<?php
		} elseif ($dstpage == "product") {
			// get db data
			
			$prods = ierg4210_prod_fetchAll();
			$pidOptions = '';

			foreach ($prods as $value) {
				$pidOptions .= "<option value=\"$value[PID]\">{htmlspecialchars($value['NAME'])}</option>";
			}
		?>
			<h3 class="my-4 text-start">Products</h3>
			<table class="db-table mb-4">
				<tr>
					<th></th>
					<th><button id="prod_add_button"><i class="bi bi-database-add"></i></button></th>
					<th>PID</th>
					<th>Category</th>
					<th>Name</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Description</th>
					<th></th>
				</tr>
				<?php
				foreach ($prods as $value) {
					$cat_name = find_name_by_cid($cats, $value['CID']);
					$productRow = "<tr class=\"prod-row\">
					<td><button><i class=\"bi bi-trash-fill\"></i></button></td>
					<td><button><i class=\"bi bi-pencil-fill\"></i></button></td>
					<td>$value[PID]</td>
					<td data-cid=\"$value[CID]\">$cat_name</td>
					<td>{htmlspecialchars($value['NAME'])}</td>
					<td>$value[PRICE]</td>
					<td>$value[QUANTITY]</td>
					<td>{htmlspecialchars($value['DESCRIPTION'])}</td>
					</tr>";
					echo $productRow;
				}
				?>
			</table>
			<fieldset class="px-4 text-start w-75 mb-4 d-none">
				<legend>Add New Product</legend>
				<form id="prod_insert" method="POST" action="admin_process.php?action=prod_insert" enctype="multipart/form-data">
					<label for="prod_insert_cid">Category</label>
					<select id="prod_insert_cid" class="mb-3" name="cid"><?= $cidOptions ?></select>
					<label for="prod_insert_name">Name</label>
					<input id="prod_insert_name" class="mb-3" type="text" name="name" required pattern="^[\w\- ]+$"/>
					<label for="prod_insert_price">Price</label>
					<input id="prod_insert_price" class="mb-3" type="text" name="price" required pattern="^\d+\.?\d*$"/>
					<label for="prod_insert_quantity">Quantity</label>
					<input id="prod_insert_quantity" class="mb-3" type="number" name="quantity" min="0" required/>
					<label for="prod_insert_desc">Description</label>
					<textarea id="prod_insert_desc" class="mb-3" name="description" required></textarea>
					<label for="prod_insert_file">Image</label>
					
					<div>
						<div class="drop_area text-center position-relative">
							<span class="choose-file-button px-2 py-1 me-3">Choose files</span>
							<span class="drop-here-text">or drag and drop files here</span>
							<p class="text-danger d-none size-alert">File size must be <= 5MB</p>
							<input type="file" name="file" id="prod_insert_file" class="mb-3 file-input" required accept="image/png, image/jpeg, image/gif"/>
						</div>
						<p>Preview: <span class="filename-text"></span></p>
						<img class="preview-thumbnail mb-3 d-none" width="160" height="160">
					</div>
					<input class="mb-3" type="submit" value="Add"/>
					<input type="hidden" name="nonce" value="<?= csrf_getNonce('prod_insert') ?>" />
				</form>
			</fieldset>

			<fieldset class="px-4 text-start w-75 mb-4 d-none">
				<legend>Edit Product</legend>
				<form id="prod_edit" method="POST" action="admin_process.php?action=prod_edit" enctype="multipart/form-data">
					<label for="prod_edit_pid">Product</label>
					<select id="prod_edit_pid" class="mb-3 prod-edit-input" name="pid"><?= $pidOptions ?></select>
					<label for="prod_edit_cid">Category</label>
					<select id="prod_edit_cid" class="mb-3 prod-edit-input" name="cid"><?= $cidOptions ?></select>
					<label for="prod_edit_name">Name</label>
					<input id="prod_edit_name" class="mb-3 prod-edit-input" type="text" name="name" required pattern="^[\w\- ]+$"/>
					<label for="prod_edit_price">Price</label>
					<input id="prod_edit_price" class="mb-3 prod-edit-input" type="text" name="price" required pattern="^\d+\.?\d*$"/>
					<label for="prod_edit_quantity">Quantity</label>
					<input id="prod_edit_quantity" class="mb-3 prod-edit-input" type="number" name="quantity" min="0" required/>
					<label for="prod_edit_desc">Description</label>
					<textarea id="prod_edit_desc" class="mb-3 prod-edit-input" name="description" required></textarea>
					<label for="prod_edit_file">Image</label>
					<div>
						<div class="drop_area text-center position-relative">
							<span class="choose-file-button px-2 py-1 me-3">Choose files</span>
							<span class="drop-here-text">or drag and drop files here</span>
							<p class="text-danger d-none size-alert">File size must be <= 5MB</p>
							<input type="file" name="file" id="prod_edit_file" class="mb-3 file-input" required accept="image/png, image/jpeg, image/gif"/>
						</div>
						<p>Preview: <span class="filename-text"></span></p>
						<img class="preview-thumbnail mb-3 d-none" width="160" height="160">
					</div>
					<input class="mb-3" type="submit" value="Edit"/>
					<input type="hidden" name="nonce" value="<?= csrf_getNonce('prod_edit') ?>" />
				</form>
			</fieldset>

			<form method="POST" id="prod_del" class="d-none" action="admin_process.php?action=prod_delete">	
				<input type="hidden" name="pid" value="0" required />
				<input type="hidden" name="nonce" value="<?= csrf_getNonce('prod_delete') ?>" />
			</form>
		<?php
		} else {
			echo "<h2 class=\"text-start my-4\">Welcome to Admin Page</h2>";
		}
		?>
	</div>
</section>


<script src="js/admin.js" defer></script>
{% endcapture %}

{% include_relative _layouts/default.html %}

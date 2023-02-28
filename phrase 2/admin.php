<?php
session_start();

// check login info
if (!isset($_SESSION['isadmin']) || !$_SESSION['isadmin']) {
	header('Location: admin_login.php');
	exit();
}
?>



<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>HungryShop</title>
	<meta name="description" content="HungryShop Online Store">
	<meta name="author" content="HungryShop">
	<link rel="icon" href="img/icon.png">
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

	<link rel="stylesheet" href="css/global.css">
</head>
<body>
	<header class="container-fluid p-0">
		<div class="d-flex justify-content-center p-0 my-4">
			<a class="p-0 mx-2 nav-link text-light fw-bold" href="index.php" id="brandlink">
				<img src="img/icon.png" alt="icon" width="223" height="50"> <!-- font from https://www.fontspace.com/category/logo -->
			</a>
		</div>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark px-4">
			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCatagory" aria-controls="navbarCatagory" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="navbar-collapse collapse text-center justify-content-center" id="navbarCatagory">
				<ul class="navbar-nav">
					<li class="nav-item mx-auto p-2">
						<a class="nav-link text-danger" href="admin.php">Admin Panel</a>
					</li>
					<li class="nav-item mx-auto p-2">
						<a class="nav-link text-white" href="index.php">Overview</a>
					</li>
					<?php
					require_once __DIR__.'/lib/db.inc.php';
					$cats = ierg4210_cat_fetchAll();
					foreach ($cats as $value) {
						$nav_link = "<li class=\"nav-item mx-auto p-2\">
							<a class=\"nav-link text-white\" href=\"category.php?cid=$value[CID]\">$value[NAME]</a>
						</li>";
						echo $nav_link;
					}
					?>
					
					<!-- offcanvas <button class="btn text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart"><i class="bi bi-cart"></i></button> -->
					<li id="CartList" class="nav-item mx-auto p-2">
						<button class="btn text-white" type="button" aria-controls="Cart Item" id="CartItemIcon"><i class="bi bi-cart"></i></button>
						<div class="bg-white p-4 my-0">
							<ul class="list-group">
								<li class="list-group-item">
									<div>
										<img src="img/product/apple.jpg" width="64" height="64" class="mx-4"> <span class="mx-4">Apple</span>
										<input type="number" name="quantity" min="1" max="9999" value="5" id="apple-input-quantity" class="mx-4">
									</div>
								</li>
							</ul>
							<button type="button" class="add-cart d-block my-4"><i class="bi bi-paypal"></i> Checkout</button>
						</div>
					</li>
					
				</ul>
			</div>
		</nav>
	</header>

	<!-- shopping list offcanvas
	<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="offcanvasCartLabel">1 item in Shopping Cart</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<hr>
		<div class="offcanvas-body">
			<div>
				<img src="img/product/apple.jpg" width="64" height="64" class="mx-4"> <span class="mx-4">Apple</span>
				<input type="number" name="quantity" min="1" max="9999" value="5" id="apple-input-quantity" class="mx-4">
			</div>
			<p>Your shopping cart is empty.</p>

			<button type="button" class="add-cart d-block my-4"><i class="bi bi-paypal"></i> Checkout</button>
		</div>
	</div>
	-->


	
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
		$cidOptions .= "<option value=\"$value[CID]\">$value[NAME]</option>";
	}
	?>
	<section class="row mx-auto text-center">
		<div class="col-3 px-0 admin-nav">
			<ul class="nav flex-column">
				<li class="nav-item py-2"><a href="admin.php?page=category" class="nav-link text-black">Categories</a></li>
				<li class="nav-item py-2"><a href="admin.php?page=product" class="nav-link text-black">Products</a></li>
				<li class="nav-item py-2"><a href="admin_login.php?logout=1" class="nav-link text-black">Logout</a></li>
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
						<td>$value[CID]</td>
						<td>$value[NAME]</td>
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
					</form>
				</fieldset>
				<fieldset class="px-4 text-start w-75 mb-4 d-none">
					<legend>Edit Category</legend>
					<form method="POST" id="cat_edit" action="admin_process.php?action=cat_edit">
						<select id="cat_edit_cid" class="mb-3 d-block" name="cid"><?= $cidOptions ?></select>
						<input id="cat_edit_name" class="mb-3" type="text" name="name" required pattern="^[\w\- ]+$" placeholder="Enter Category Name" />
						<input type="submit" value="Edit" />
					</form>
				</fieldset>
				<form method="POST" id="cat_del" class="d-none" action="admin_process.php?action=cat_delete">	
					<input type="hidden" name="cid" value="0" required />
				</form>
			<?php
			} elseif ($dstpage == "product") {
				// get db data
				
				$prods = ierg4210_prod_fetchAll();
				$pidOptions = '';
	
				foreach ($prods as $value) {
					$pidOptions .= "<option value=\"$value[PID]\">$value[NAME]</option>";
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
						<td>$value[NAME]</td>
						<td>$value[PRICE]</td>
						<td>$value[QUANTITY]</td>
						<td>$value[DESCRIPTION]</td>
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
					</form>
				</fieldset>
	
				<form method="POST" id="prod_del" class="d-none" action="admin_process.php?action=prod_delete">	
					<input type="hidden" name="pid" value="0" required />
				</form>
			<?php
			} else {
				echo "<h2 class=\"text-start my-4\">Welcome to Admin Page</h2>";
			}
			?>
		</div>
		
	</section>
	<script src="js/admin.js" defer></script>


	<footer class="py-3 bg-dark text-center text-light">
		<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><i class="bi bi-instagram text-warning"></i></a> &nbsp;
		<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><i class="bi bi-facebook"></i></a> &nbsp;
		Email: testing123@gmail.com<br>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>

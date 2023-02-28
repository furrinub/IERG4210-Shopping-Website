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


	<?php
	$cid = $_GET['cid'] ?? '1';
	if (!preg_match('/^\d*$/', $cid))
		$cid = '1';
	$cid = (int) $cid;
	if ($cid >= count($cats))
		$cid = 1;
	?>
	
	<section class="w-90 mx-auto my-4">
		<h2><a href="index.php" class="text-black">Overview</a> > <a href="#" class="text-black"><?php echo find_name_by_cid($cats, $cid); ?></a></h2>
	</section>
	<section class="text-center mb-4">
		<div class="row mx-auto text-center product-box">
			<?php
			foreach (ierg4210_prod_fetch_by_cid($cid) as $value) {
			?>
				<div class="col-5 col-md-3 px-0 mb-4 text-center">
					<a href="product.php?pid=<?= $value['PID'] ?>" class="text-decoration-none text-black">
						<img src="product_images/thumbnails/<?= $value['PID'] ?>.webp" alt="product image" width="160" height="160">
						<p><?= $value['NAME'] ?></p>
					</a>
					<hr class="w-75 mx-auto">
					<p class="price">HK$<?= $value['PRICE'] ?></p>
					<button type="button" class="add-cart"><i class="bi bi-cart"></i> Add to Cart</button>
				</div>
			<?php
			}
			?>
		</div>
	</section>


	<footer class="py-3 bg-dark text-center text-light">
		<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><i class="bi bi-instagram text-warning"></i></a> &nbsp;
		<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"><i class="bi bi-facebook"></i></a> &nbsp;
		Email: testing123@gmail.com<br>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
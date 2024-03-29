---
layout: default
---
<section class="mb-4">
	<div id="BannerCarousel" class="carousel slide" data-bs-ride="carousel">
		<div class="carousel-indicators">
			<button type="button" data-bs-target="#BannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
			<button type="button" data-bs-target="#BannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
			<button type="button" data-bs-target="#BannerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
		</div>
		<div class="carousel-inner w-75 mx-auto">
			<div class="carousel-item active" data-bs-interval="3500">
				<img src="https://images.hktvmall.com/image_slider/banneren_230119034825.jpg" class="d-block w-100" alt="...">
			</div>
			<div class="carousel-item" data-bs-interval="3500">
				<img src="https://images.hktvmall.com/image_slider/banneren_230126035334.jpg" class="d-block w-100" alt="...">
			</div>
			<div class="carousel-item" data-bs-interval="3500">
				<img src="https://images.hktvmall.com/image_slider/banneren_230126041213.jpg" class="d-block w-100" alt="...">
			</div>
		</div>
		<button class="carousel-control-prev" type="button" data-bs-target="#BannerCarousel" data-bs-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Previous</span>
		</button>
		<button class="carousel-control-next" type="button" data-bs-target="#BannerCarousel" data-bs-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Next</span>
		</button>
	</div>
</section>

<section class="w-90 mx-auto my-4">
	<h2><a href="#" class="text-black">Overview</a></h2>
</section>


<?php 
$prod = ierg4210_prod_fetchAll();
$headings = array("What's New", "Hot Sale", "Special Offers");
foreach ($headings as $h) {
?>

<section class="text-center mb-4">
	<h3><?= $h ?></h3>
	<div class="horizontal-product-list overflow-x-scroll">
		<ul class="p-0">
			<?php 
			$random_index = count($prod) == 0 ? array() : array_rand($prod, min(count($prod), 6));
			foreach ($random_index as $i) {
			?>
				<li class="w-20 home-item d-inline-block pb-2">
					<a href="product.php?pid=<?= $prod[$i]['PID'] ?>" class="text-decoration-none text-black">
						<img src="product_images/thumbnails/<?= $prod[$i]['PID'] ?>.webp" width="160" height="160">
						<p><?= htmlspecialchars($prod[$i]['NAME']) ?></p>
					</a>
					<hr>
					<p class="price">HK$<?= $prod[$i]['PRICE'] ?></p>
					<button type="button" class="add-cart" data-pid="<?= $prod[$i]['PID'] ?>"><i class="bi bi-cart"></i> Add to Cart</button>
				</li>
			<?php
			}
			?>
		</ul>
	</div>
</section>

<?php
}
?>


<section class="text-center mb-4">
	<h3>More Prodcts</h3>
	<div class="aaa-horizontal-product-list">
		<ul id="more_prod_list" class="p-0 row w-90 mx-auto"></ul>
	</div>
</section>

<section class="text-center">
	<img src="./img/loading.svg">
</section>
<!--
<div class="permission-denied w-100 fixed-bottom">
	<p class="alert-text text-center">You do not have permission to access admin panel.</p>
</div>
-->
<script defer src="./js/inf_scroll.js"></script>

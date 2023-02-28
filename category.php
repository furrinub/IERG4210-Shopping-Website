---
layout: default
---
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
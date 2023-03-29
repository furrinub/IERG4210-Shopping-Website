---
layout: default
---

<?php
try {
    $prod = ierg4210_prod_fetchOne();
} catch (Exception $e) {
    header('Location: index.php');
    exit();
}

$quantity_display = (int) $prod['QUANTITY'];
if ($quantity_display <= 3) $quantity_display = "Only $quantity_display left!";
?>

<section class="w-90 mx-auto my-4">
	<h2>
		<a href="index.php" class="text-black">Overview</a> > 
		<a href="category.php?cid=<?= $prod['CID'] ?>" class="text-black"><?= find_name_by_cid($cats, $prod['CID']); ?></a> > 
		<a href="#" class="text-black"><?= htmlspecialchars($prod['NAME']) ?></a>
	</h2>
</section>
<section class="mb-4 mx-auto w-90">
	<div class="row justify-content-center">
		<div class="col-4 product-big-picture p-0">
			<img src="product_images/<?= $prod['PID'] ?>.webp" width="100%">
		</div>
		<div class="col-1"></div>
		<div class="col-6">
			<h3><?= htmlspecialchars($prod['NAME']) ?></h3>
			<p><?= htmlspecialchars($prod['DESCRIPTION']) ?></p>
			<p class="mb-4">Quantity in Stock: <?= $quantity_display ?></p>
			<hr class="w-75">
			<h3 class="price mb-4">HK$<?= $prod['PRICE'] ?></h3>
			<p class="mb-4">Buy: <input type="number" name="quantity" id="buy_quantity" min="1" max="9999" value="1"></p>
			<button type="button" class="add-cart p-2" data-pid="<?= $prod['PID'] ?>"><h4 class="m-0"><i class="bi bi-cart"></i> Add to Cart</h4></button>
		</div>
	</div>
</section>

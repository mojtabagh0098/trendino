<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="tno-acc-page" dir="rtl">
	<div class="tno-acc-container">
		<div class="tno-acc-layout">
			<aside class="tno-acc-sidebar">
				<?php do_action( 'woocommerce_account_navigation' ); ?>
			</aside>

			<main class="tno-acc-content">
				<?php do_action( 'woocommerce_account_content' ); ?>
			</main>
		</div>
	</div>
</div>
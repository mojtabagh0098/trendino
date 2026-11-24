<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user         = wp_get_current_user();
$full_name    = trim( $user->first_name . ' ' . $user->last_name );
$display_name = $full_name !== '' ? $full_name : $user->display_name;
$phone        = (string) get_user_meta( $user->ID, 'billing_phone', true );
if ( $phone === '' ) {
	$phone = (string) get_user_meta( $user->ID, 'digits_phone_no', true );
}
if ( $phone === '' && preg_match( '/^09\d{9}$/', $user->user_login ) ) {
	$phone = $user->user_login;
}

$icons = [
	'dashboard'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
	'orders'           => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
	'edit-address'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
	'tno-reviews'      => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
	'tno-wishlist'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
	'tno-recent-views' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
	'tno-club'         => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5.8 11.3L2 22l10.7-3.8"/><path d="M4 3h.01M22 8h.01M15 2h.01M22 20h.01"/><path d="M22 2l-2.24.75a2.9 2.9 0 0 0-1.96 3.12v0c.1.86-.57 1.63-1.45 1.63h-.38c-.86 0-1.6.6-1.76 1.44L14 10"/><path d="M11 13l9-9"/></svg>',
	'edit-account'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
	'customer-logout'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
];

$is_view_order = is_wc_endpoint_url( 'view-order' );
$menu_items    = wc_get_account_menu_items();
?>

<nav class="tno-acc-nav-card" aria-label="منوی حساب کاربری">
	<div class="tno-acc-profile">
		<div class="tno-acc-avatar">
			<?php echo get_avatar( $user->ID, 48 ); ?>
		</div>
		<div class="tno-acc-profile-info">
			<strong class="tno-acc-profile-name"><?php echo esc_html( $display_name ); ?></strong>
			<?php if ( $phone !== '' ) : ?>
				<span class="tno-acc-profile-phone" dir="ltr"><?php echo esc_html( tno_fa_digits( $phone ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<ul class="tno-acc-menu">
		<?php foreach ( $menu_items as $endpoint => $label ) :
			$classes = wc_get_account_menu_item_classes( $endpoint );
			if ( $endpoint === 'orders' && $is_view_order ) {
				$classes .= ' is-active';
			}
			$is_logout = ( $endpoint === 'customer-logout' );
			?>
			<li class="tno-acc-menu-item <?php echo esc_attr( $classes ); ?> <?php echo $is_logout ? 'tno-acc-menu-item--logout' : ''; ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="tno-acc-menu-link">
					<span class="tno-acc-menu-icon" aria-hidden="true">
						<?php echo $icons[ $endpoint ] ?? $icons['dashboard']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="tno-acc-menu-label"><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
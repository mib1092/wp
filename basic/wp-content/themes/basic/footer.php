<?php
    if ( function_exists( 'ot_get_option' ) ) {
      $footer_logo = ot_get_option( 'footer_logo', get_bloginfo("template_url") . '/img/main_logo.png' );
      $adds = ot_get_option( 'adds', '5th avenue, E Santa clara street, Silicon valley.' );
      $email = ot_get_option( 'email', 'support@andrewkutsenda.com' );
      $phone = ot_get_option( 'phone', '00 123 4567 890' );
      $clean_tel = preg_replace('/[^0-9]/', '', $phone);
      $fb = ot_get_option( 'fb', '#' );
      $tw = ot_get_option( 'tw', '#' );
      $inst = ot_get_option( 'inst', '#' );
      $in = ot_get_option( 'in', '#' );
    }
?>
		<footer class="footer">

		</footer>
	</div><!--/wrap-->

	<?php wp_footer(); ?>
</body>
</html>
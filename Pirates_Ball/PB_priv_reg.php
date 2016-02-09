<?php
/*
Template Name: PB Priv Reg
*/
global $defSel, $wpdb;

use CTXPHC\BeachHoliday\Classes\PB_Reg;

define( 'BH_CLASSES', trailingslashit( get_template_directory() ) . trailingslashit( 'includes/Class' ) );

if ( ! class_exists( 'PB_Reg' ) ) {
	require_once( BH_CLASSES . 'class-PB_Reg.php' );
}

$args             = array();
$args[ 'states' ] = get_states_array();

$pb_cost       = 70.00;
$pb_early_cost = 60.00;
$pb_memb_cost  = 50.00;
$pb_priv_cost  = 50.00;


$args[ 'pb_today' ]    = $pb_today = new DateTime();
$args[ 'expiry' ]      = $expiry = new DateTime( "July 1, 2015 12:00:00" );
$args[ 'expiry2' ]     = $expiry2 = new DateTime( "August 1, 2015 12:00:00" );
$args[ 'table' ]       = $table = 'ctxphc_pb_reg';
$args[ 'pb_priv_reg' ] = true;

if ( $args[ 'pb_priv_reg' ] ) {
	$args[ 'pb_memb_class' ]  = $pb_memb_class = 'pb_hidden';
	$args[ 'pb_early_class' ] = $pb_early_class = 'pb_hidden';
	$args[ 'pb_late_class' ]  = $pb_late_class = 'pb_hidden';
	$args[ 'pb_priv_class' ]  = $pb_priv_class = 'pb_display';
	$args[ 'pb_priv_cost' ]   = $pb_reg_cost = $pb_memb_cost;
} else if ( $pb_today >= $expiry && $pb_today <= $expiry2 ) {
	$args[ 'pb_memb_class' ]  = $pb_memb_class = 'pb_hidden';
	$args[ 'pb_early_class' ] = $pb_early_class = 'pb_display';
	$args[ 'pb_late_class' ]  = $pb_late_class = 'pb_hidden';
	$args[ 'pb_priv_class' ]  = $pb_priv_class = 'pb_hidden';
	$args[ 'pb_reg_cost' ]    = $pb_reg_cost = $pb_early_cost;
} else if ( $pb_today > $expiry2 ) {
	$args[ 'pb_memb_class' ]  = $pb_memb_class = 'pb_hidden';
	$args[ 'pb_early_class' ] = $pb_early_class = 'pb_hidden';
	$args[ 'pb_late_class' ]  = $pb_late_class = 'pb_display';
	$args[ 'pb_priv_class' ]  = $pb_priv_class = 'pb_hidden';
	$args[ 'pb_reg_cost' ]    = $pb_reg_cost = $pb_cost;
} else {
	$args[ 'pb_memb_class' ]  = $pb_memb_class = 'pb_display';
	$args[ 'pb_early_class' ] = $pb_early_class = 'pb_hidden';
	$args[ 'pb_late_class' ]  = $pb_late_class = 'pb_hidden';
	$args[ 'pb_priv_class' ]  = $pb_priv_class = 'pb_hidden';
	$args[ 'pb_reg_cost' ]    = $pb_reg_cost = $pb_memb_cost;
}

get_header(); ?>
<!--suppress ALL -->
<script type="text/javascript">
	<!--
	//--------------------------------
	// This code compares two fields in a form and submit it
	// if they're the same, or not if they're different.
	//--------------------------------
	function checkEmail(theForm) {
		if (theForm.pb_email.value != theForm.pb_email_verify.value) {
			alert('Those emails don\'t match!');
			$ret_val = false;
		} else {
			$ret_val = true;
		}
		return $ret_val;
	}
	//-->
</script>

<div id="content">
	<div class="spacer"></div>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div id="post_title" class="post_title">
				<h1><a href="<?php the_permalink() ?>" rel="bookmark"
				       title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			</div>
			<!-- Post_title -->

			<div class="clear"></div>

			<div class="entry">
				<?php the_content( 'more...' ); ?>
				<div class="clear"></div>

				<div class="spacer"></div>

				<div class="pb_header">
					<h2 class="pieces_of_eight">CTXPHC 2015 Pirate's Ball</h2>

					<h2 class="pb_center <?php echo $pb_priv_class; ?>" id="memb_reg">Private Registration</h2>
				</div>

				<div class="spacer"></div>

				<div>
					<img id="PB_logo" alt="CTXPHC Pirate's Ball 2015 Logo"
					     src="http://www.ctxphc.com/wp-content/themes/beach-holiday/includes/Images/Pirates_Ball/2015-Pirates-Ball-Logo.jpg"
					     width="300">
				</div>
				<div class="pb_cost <?php echo $pb_priv_class; ?>" id="memb_reg_cost">
					<h4 class="pb_center pb_header">CTXPHC Private Registration Cost: $<?php echo $pb_memb_cost; ?> per person</h4>
				</div>
				<p class="pb_center pb_details">
					<a class="pb_details_link" href="https://www.ctxphc.com/pirates-ball-details/">
						Click here for additional event and hotel information!
					</a>
				</p>


				<?php
				if ( isset( $_POST[ 'submit' ] ) ) {
					$clean_post_data = array_map( 'mysql_real_escape_string', $_POST );
					if ( ! isset( $clean_post_data[ 'attendee_count' ] ) ) {
						$clean_post_data[ 'attendee_count' ] = 1;
					}

					foreach ( $clean_post_data as $ckey => $cval ) {
						error_log( $ckey . ' ------------> ' . $cval );
					}
					$memb_pb_reg    = new PB_Reg( $args );
					$pb_loaded_data = $memb_pb_reg->load_user_data( $clean_post_data );
					foreach ( $pb_loaded_data as $lkey => $lval ) {
						error_log( $lkey . ' ------> ' . $lval );
					}
					$pb_data_insert_results = $memb_pb_reg->pb_data_insert( $table, $pb_loaded_data );
					error_log( $pb_data_insert_results );

					$form_type = 'review';
					$memb_pb_reg->display_pb_form( $form_type, $pb_data_insert_results );
				} else {
					$form_type   = 'new';
					$memb_pb_reg = new PB_Reg( $args );
					$memb_pb_reg->display_pb_form( $form_type );
				}
				?>

			</div>
			<!-- entry -->
		</div> <!-- post -->
		<?php
	endwhile;
	endif;
	?>
</div> <!-- content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>

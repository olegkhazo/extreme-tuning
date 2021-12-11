<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Inspiro
 * @subpackage Inspiro_Lite
 * @since Inspiro 1.0.0
 * @version 1.0.0
 */

get_header(); ?>

<div class="inner-wrap">

	<header class="page-header">
		<?php if ( have_posts() ) : ?>
			<h1 class="page-title">
			<?php
			/* translators: Search query. */
			printf( esc_html__( 'Результаты поиска для: %s', 'inspiro' ), '<span>' . get_search_query() . '</span>' );
			?>
			</h1>
		<?php else : ?>
			<h1 class="page-title"><?php esc_html_e( 'Ничего не найдено', 'inspiro' ); ?></h1>
		<?php endif; ?>
	</header><!-- .page-header -->

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :
			// Start the Loop.
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/post/content', 'excerpt' );
			endwhile; // End the loop.

			the_posts_pagination(
				array(
					'prev_next' => false,
				)
			);
		else :
			?>

			<p><?php esc_html_e( 'Извините, но ничего не соответствует вашим поисковым запросам. Пожалуйста, попытайтесь снова с другими ключевыми словами.', 'inspiro' ); ?></p>
			<?php
				get_search_form();
		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

</div><!-- .inner-wrap -->

<?php
get_footer();

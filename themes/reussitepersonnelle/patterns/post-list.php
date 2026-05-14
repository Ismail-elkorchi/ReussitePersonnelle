<?php
/**
 * Title: Post list
 * Slug: reussitepersonnelle/post-list
 * Categories: posts
 *
 * @package ReussitePersonnelle
 */
?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"rp-latest-query","layout":{"type":"default"}} -->
<div class="wp-block-query rp-latest-query">
	<!-- wp:post-template {"layout":{"type":"grid","columnCount":2}} -->
		<!-- wp:group {"tagName":"article","className":"rp-post-card","layout":{"type":"constrained"}} -->
		<article class="wp-block-group rp-post-card">
			<!-- wp:post-terms {"term":"category"} /-->
			<!-- wp:post-title {"isLink":true} /-->
			<!-- wp:post-excerpt {"moreText":"Lire la suite","excerptLength":24} /-->
			<!-- wp:post-date /-->
		</article>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->

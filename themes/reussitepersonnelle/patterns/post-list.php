<?php
/**
 * Title: Post list
 * Slug: reussitepersonnelle/post-list
 * Categories: posts
 *
 * @package ReussitePersonnelle
 */
?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query">
	<!-- wp:post-template -->
		<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->
		<!-- wp:post-excerpt {"moreText":"Lire la suite"} /-->
		<!-- wp:separator -->
		<hr class="wp-block-separator has-alpha-channel-opacity"/>
		<!-- /wp:separator -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->

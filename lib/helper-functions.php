<?php
function fr_ar_links($frLink){
	$reussitepersonnelleBaseUrl = "https://www.reussitepersonnelle.com";
	if (! is_rtl() ) {
		echo $reussitepersonnelleBaseUrl . $frLink;
	} else {
		switch ($frLink) {
			case '/changer-de-vie/':
				echo $reussitepersonnelleBaseUrl . "/ar/%D9%84%D9%86%D8%BA%D9%8A%D8%B1-%D8%AD%D9%8A%D8%A7%D8%AA%D9%86%D8%A7/";
				break;

			case '/newsletter/':
				echo $reussitepersonnelleBaseUrl . "/ar/";
				break;

			case '/blog/':
				echo $reussitepersonnelleBaseUrl . "/ar/%D8%A7%D9%84%D9%85%D8%AF%D9%88%D9%86%D8%A9/";
				break;

			case '/a-propos/':
				echo $reussitepersonnelleBaseUrl . "/ar/%D9%85%D9%80%D9%80%D9%86-%D9%86%D8%AD%D9%80%D9%80%D9%80%D9%86/";
				break;

			case '/politique-confidentialite/':
				echo $reussitepersonnelleBaseUrl . "/ar/";
				break;

			case '/politique-de-remboursement/':
				echo $reussitepersonnelleBaseUrl . "/ar/%D8%B3%D9%8A%D8%A7%D8%B3%D8%A9-%D8%A5%D8%B9%D8%A7%D8%AF%D8%A9-%D8%A7%D9%84%D8%A3%D9%85%D9%88%D8%A7%D9%84/";
				break;
		}
	}
}
 ?>

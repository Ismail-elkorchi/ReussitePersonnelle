<div class="front-left">
	<section id="text-20" class="widget widget_text">
		<div class="widget-wrap">
			<h4 class="widget-title widgettitle"><?php esc_html_e('Concevez une vie simple.', 'reussitepersonnelle'); ?></h4>
			<div class="textwidget">
				<p><?php esc_html_e('Les secrets du bien-&ecirc;tre et de l&rsquo;apaisement psychologique se pr&eacute;sentent sous la forme de 7 articles gratuits.', 'reussitepersonnelle'); ?></p>
				<p><?php esc_html_e('Le but de ces derniers est de vous redonner les r&ecirc;nes de votre vie, vous permettant ainsi de vous affirmer et d&rsquo;enfin profiter pleinement du temps qui vous est imparti.', 'reussitepersonnelle'); ?></p>
			</div>
		</div>
	</section>
</div>

<div class="front-right">
	<div class="newsletter">

		<h4 class="widget-title"><?php esc_html_e('Newsletter gratuite', 'reussitepersonnelle'); ?></h4>
		<p class="center"><?php esc_html_e('Livr&eacute;e &agrave; votre bo&icirc;te de r&eacute;ception.', 'reussitepersonnelle'); ?></p>
		<div id="mc_embed_signup">

			<form action="https://www.reussitepersonnelle.com/sendy/subscribe" method="POST" accept-charset="utf-8">

				<div id="mc_embed_signup_scroll">

					<div class="mc-field-group">
						<label for="name"><?php esc_html_e('Pr&eacute;nom', 'reussitepersonnelle'); ?></label>
						<input type="text" name="name" id="name" autocomplete="given-name"/>
					</div>

					<div class="mc-field-group">
						<label for="email"><?php esc_html_e('Email', 'reussitepersonnelle'); ?></label><br/>
						<input type="email" name="email" id="email" autocomplete="email"/>
					</div>
					<div class="clear"></div>
					<input type="hidden" name="list" value="<?php echo is_rtl() ? 'k2YTa49e3UNxNdleJWwY763w' : 's892DRweYGXUU9hnxLNe1dsQ'; ?>"/>
					<input type="submit" value="<?php esc_html_e('Rejoindre gratuitement', 'reussitepersonnelle'); ?>" name="submit" id="submit" class="button">
				</div>
			</form>
		</div>
	</div>
</div>

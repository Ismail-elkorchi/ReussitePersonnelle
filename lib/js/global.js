jQuery(function( $ ){

	$( '.nav-primary .genesis-nav-menu, .nav-secondary .genesis-nav-menu' ).addClass( 'responsive-menu' ).before('<a href="#" class="responsive-menu-icon">Menu<span></span></a>');


	$( '.responsive-menu-icon' ).click(function(event){
		event.preventDefault();
		$(this).next( '.nav-primary .genesis-nav-menu,  .nav-secondary .genesis-nav-menu' ).slideToggle();
		$('.site-header').toggleClass('nav-is-visible');
	});

	$( window ).resize(function(){
		if ( window.innerWidth > 800 ) {
			$( '.nav-primary .genesis-nav-menu,  .nav-secondary .genesis-nav-menu, nav .sub-menu' ).removeAttr( 'style' );
			$( '.responsive-menu > .menu-item' ).removeClass( 'menu-open' );
		}
	});

	$( '.responsive-menu > .menu-item' ).click(function(event){
		if ( event.target !== this )
		return;
			$(this).find( '.sub-menu:first' ).slideToggle(function() {
			$(this).parent().toggleClass( 'menu-open' );
		});
	});

	//* Auto-Hiding Navigation
	var mainHeader = $('.site-header');

	//Set scrolling variables
	var scrolling = false,
		previousTop = 0,
		currentTop = 0,
		scrollDelta = 10,
		scrollOffset = 150;

	$(window).on('scroll', function(){
		if( !scrolling ) {
			scrolling = true;
			(!window.requestAnimationFrame)
				? setTimeout(autoHideHeader, 250)
				: requestAnimationFrame(autoHideHeader);
		}
	});

	function autoHideHeader() {
		var currentTop = $(window).scrollTop();
		checkNavigation(currentTop);
		previousTop = currentTop;
		scrolling = false;
	}

	function checkNavigation(currentTop) {
			if (previousTop - currentTop > scrollDelta) {
				//if scrolling up...
				mainHeader.removeClass('is-hidden');
			} else if( currentTop - previousTop > scrollDelta && currentTop > scrollOffset) {
				//if scrolling down...
				mainHeader.addClass('is-hidden');
			}
	}


  var n = 0;
    $('#main-nav-search-link').click(function(){
      if(n == 0) {
		$('.search-div').show('slow');
        n = 1;
      }else{
          $('.search-div').hide('slow');
          n = 0;
      }
	});


	$("*", document.body).click(function(event){
		// event.stopPropagation();
		var el = $(event.target);
		var gsfrm = $(el).closest('form');
		if(el.attr('id') !='main-nav-search-link' && el.attr('role') != 'search' && gsfrm.attr('role') != 'search'){
			$('.search-div').hide('slow');
            n = 0;
		}
	});



	$('.search-form').submit(function(e) { // run the submit function, pin an event to it
        var s = $( this ).find('[name="s"]').val($.trim($( this ).find('[name="s"]').val())); // find the #s, which is the search input id and trim any spaces from the start and end
        if (!s.val()) { // if s has no value, proceed
            e.preventDefault(); // prevent the default submission
           // $('[name="s"]').attr("placeholder", "Vous avez oublier de saisir votre requette! Hein ?");
           $('[name="s"]').focus(); // focus on the search input
        }
    });


    // Social Sharing
	$( '.share-button' ).click( function( e ) {
		e.preventDefault();
		sharewindow = window.open( $( this ).find("a").attr( 'href' ), $( this ).find("a").data( 'name' ), 'height=550,width=500' );
		if ( window.focus ) {
			sharewindow.focus();
		}
	});

	// Comment avatar background color
	var colors = ["1abc9c", "2ecc71", "3498db", "9b59b6", "34495e", "16a085", "27ae60", "2980b9", "8e44ad", "f1c40f", "e67e22", "e74c3c", "f39c12", "d35400", "c0392b"];
	$('#comments .avatar').each(function(i) {
	   $(this).css('background-color', '#'+colors[i % colors.length]);
	});

});

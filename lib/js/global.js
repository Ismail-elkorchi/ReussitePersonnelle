jQuery(function( $ ){

	$( '.nav-primary .genesis-nav-menu, .nav-secondary .genesis-nav-menu' ).addClass( 'responsive-menu' ).before('<div class="responsive-menu-icon"></div>');


	$( '.responsive-menu-icon' ).click(function(){
		$(this).next( '.nav-primary .genesis-nav-menu,  .nav-secondary .genesis-nav-menu' ).slideToggle();
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
    
    
    
    // Add reveal class to sticky message when scrolling up
    var position = $(document).scrollTop();
	$(document).on("scroll", function(){
        
         var scroll = $(document).scrollTop();
        
		if($(document).scrollTop() > position){

			$(".sticky-message").removeClass("reveal");		

		} else {

			$(".sticky-message").addClass("reveal");

		}
        
        position = scroll;
	});
    

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


});
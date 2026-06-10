<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" class="search-form__input" placeholder="Search&hellip;" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="btn btn--primary"><span>Search</span></button>
</form>

<section id="admin">

	<?php 
    	if ($errors !== "") {
    	    echo "<div class=\"errorMessage\"><p>$errors</hp></div>";
    	}
    	echo $clientTemplate; 
    ?>

</section>
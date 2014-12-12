(function($, W, D, undefined) {

	function poll() {

    	//update the phones list via ajax
    	$.ajax({ url: "api/getPhonesInRange.php", success: function(data) {

    		//console.log(data);
    		$('#phones').html(data.html);

    	}, dataType: "json", complete: poll, timeout: 50000 });

	}

    $(document).ready(function() {

    	//poll the request to update phones
		poll();

    });

})(jQuery, this, this.document);
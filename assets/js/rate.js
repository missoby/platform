$(document).ready(function(){
	$('#default-demo').on('click', 'img', function(){
		var tt = $(this).closest('#default-demo').find(":hidden").val();
		var idp = $(this).closest('#default-demo').data('idp');
//		alert(tt);
//                alert(idp);
		//$tab = array('note' => tt, 'id' =>  idp);
                $tab = 'note='+ tt + '&id=' + idp;
		$.ajax({
			type: 'post',
			data: $tab,
			url: 'http://localhost:8081/note/note/savenote', 
			success: function(note){
                            $('#etoileselect').remove();
                            var kk='<li id="etoileselect">';
                            for(i=1; i<=note; i++)
                                kk += '<img src="http://localhost:8081/assets/img/star-on.png">';
                            kk = kk +'</li>';
                        $('#etoilrate').after(kk);
			},
			error: function(){
				alert('erreur');
			}
		});
	});
	
});
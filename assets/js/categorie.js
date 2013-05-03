$(document).ready(function(){
    $('#catselect').on('change', function(){
        $('#divsouscat').remove();
        $('#divsoussouscat').remove();
        var id = $(this).find(":selected").data('id');
        $.ajax({
            url:'http://localhost:8081/produit/produit/getsouscategorie',
            data: 'id='+id,
            type: 'POST',
            dataType : 'json',
            success: function(cat){                    
                  var valeur = ''; 
                   valeur += '<div id="divsouscat" class="control-group">';
                             valeur +=  '<label class="control-label" for="cat" >Sous Catégorie <span class="required">*</span></label> <div class="controls">  <select id="souscatselect" name="souscat" onchange="addsouscat()"><option selected="selected"></option>'
                                        for (key in cat['sous']) 
                                        {
                                            valeur +=  '<option  data-idsous =" '+ cat['sous'][key]['idsouscategorie']+'" value="'+cat['sous'][key]['idsouscategorie']+'" >';
                                               valeur += cat['sous'][key]['titre']+'</option>';
                                        }
                                  valeur +=  '</select>';                                 
                               valeur += '</div>';
                           valeur += '</div>';
                           
  
              $('#categorieplace').after(valeur);
            },
            error: function(){
                alert('erreur');
            }
        });     
    });      
    
});


function addsouscat(){
        $('#divsoussouscat').remove();
         var id = $('#souscatselect').find(":selected").data('idsous');
        $.ajax({
            url:'http://localhost:8081/produit/produit/getsoussouscategorie',
            data: 'id='+id,
            type: 'POST',
            dataType : 'json',
            success: function(cat){
                  var valeur = ''; 
                   valeur += '<div id="divsoussouscat" class="control-group">';
                             valeur +=  '<label class="control-label" for="cat" >Sous Catégorie <span class="required">*</span></label> <div class="controls">  <select id="soussouscatselect" name="soussouscat"><option selected="selected"></option>'
                                        for (key in cat['soussous']) 
                                        {
                                            valeur +=  '<option  data-idsous =" '+ cat['soussous'][key]['id']+'" value="'+cat['soussous'][key]['id']+'" >';
                                               valeur += cat['soussous'][key]['titre']+'</option>';
                                        }
                                  valeur +=  '</select>';
                              valeur += '</div>';
                           valeur += '</div>';
              
              $('#divsouscat').after(valeur);
            },
            error: function(){
                alert('erreur');
            }
        });    
     }
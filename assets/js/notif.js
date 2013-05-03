$(document).ready(function(){
setInterval(function(){ 
    $.ajax({
    url: 'http://localhost:8081/notification/notif/notifajax',
    type: "POST",
    dataType: 'json',

    success: function (msg) { 
  notifmsg = msg['msg'];
  notif = msg['action'];
 
if( notif == 0  )
{

    $('#clicknotifaction').removeClass('notifred');
    if($('#clicknotifaction:not(.notifblack)'))
        $('#clicknotifaction').addClass('notifblack');
}
else
{
  $('#clicknotifaction').removeClass('notifblack');
    if($('#clicknotifaction:not(.notifred)'))
        $('#clicknotifaction').addClass('notifred');
}    
 
if( notifmsg == 0  )
{
      $('#clicknotifmsg').removeClass('notifredmsg');
    if($('#clicknotifmsg:not(.notifblackmsg)'))
        $('#clicknotifmsg').addClass('notifblackmsg');
}
else
{
  $('#clicknotifmsg').removeClass('notifblackmsg');
    if($('#clicknotifmsg:not(.notifredmsg)'))
        $('#clicknotifmsg').addClass('notifredmsg');
}   
	
    }
	
});

}, 6000);   


});
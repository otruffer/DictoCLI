$(document).ready(function(){
  $('.dictoOpen').click(function(event){
    event.preventDefault();
    $(this).siblings('.dictoOpenable').toggle();
  });

    $('li.violation').click(function(event) {
        event.preventDefault();
        $(this).children('div.fix').toggle();
    })
});

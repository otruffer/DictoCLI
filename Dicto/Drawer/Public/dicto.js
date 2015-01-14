$(document).ready(function(){
  $('.dictoOpen').click(function(event){
    event.preventDefault();
    $(this).siblings('.dictoOpenable').toggle();
  });
});

$(document).ready(function(){
  $('.dictoOpen').click(function(event){
    event.preventDefault();
    $(this).siblings('.dictoOpenable').toggle();
  });

    $('li.violation').click(function(event) {
        event.preventDefault();
        $(this).children('div.fix').toggle();
    })

    $('a.addedViolations').click(function(event) {
        event.preventDefault();
        $(this).siblings('a.item').removeClass('active');
        $(this).addClass('active');
        $(this).parents('div.violationsContainer').find('div.adddedViolations').show();
        $(this).parents('div.violationsContainer').find('div.resolvedViolations').hide();
        $(this).parents('div.violationsContainer').find('div.allViolations').hide();
        $(this).parents('div.violationsContainer').find('li.violation').show();
    });

    $('a.resolvedViolations').click(function(event) {
        event.preventDefault();
        $(this).siblings('a.item').removeClass('active');
        $(this).addClass('active');
        $(this).parents('div.violationsContainer').find('div.adddedViolations').hide();
        $(this).parents('div.violationsContainer').find('div.resolvedViolations').show();
        $(this).parents('div.violationsContainer').find('div.allViolations').hide();
        $(this).parents('div.violationsContainer').find('li.violation').show();
    });

    $('a.allViolations').click(function(event) {
        event.preventDefault();
        $(this).siblings('a.item').removeClass('active');
        $(this).addClass('active');
        $(this).parents('div.violationsContainer').find('div.adddedViolations').hide();
        $(this).parents('div.violationsContainer').find('div.resolvedViolations').hide();
        $(this).parents('div.violationsContainer').find('div.allViolations').show();
        $(this).parents('div.violationsContainer').find('li.violation').show();
    });

    $('.search').keyup(function(){
        $(this).parents('div.violationsContainer').find('li.violation').hide();
        $(this).parents('div.violationsContainer').find('li.violation:contains('+$(this).val()+')').show();
        console.log($(this).val());
    })
});

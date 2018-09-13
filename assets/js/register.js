$(document).ready(function(){ 
    //onclick() of signup, hide sign-in form and show sign-up form
    $("#signup").click(function(){
        $("#first").slideUp("slow", function(){
           $("#second").slideDown("slow");
        });
    });
    //onclick() of signin, hide sign-up form and show sign-in form
    $("#signin").click(function(){
        $("#second").slideUp("slow", function(){
            $("#first").slideDown("slow");
        });
    });
});

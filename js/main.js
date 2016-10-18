$(function() {
    refresh_page();
});

function refresh_page() {
    $(".refresh_page").on("click", function(){
        location.reload();
    });
}
$jQu = jQuery;
/* alert Message script */
function warningMessage(message)
{
    $jQu('#alertMsg').removeClass('alert-warning').removeClass('alert-success');
    $jQu('#alertMsg').addClass("alert-warning").html(message);
    $jQu('html, body').animate({
        scrollTop: $jQu("form").offset().top -100
    }, 2000);
}
function successMessage(message)
{
    $jQu('#alertMsg').removeClass('alert-warning').removeClass('alert-success');
    $jQu('#alertMsg').addClass("alert-success").html(message);
    $jQu('html, body').animate({
        scrollTop: $jQu("form").offset().top -100
    }, 2000);
}
function warningSpecificMessage(id,message)
{
    $jQu("#"+id).removeClass('alert-warning').removeClass('alert-success');
    $jQu('#'+id).addClass("alert-warning").html(message);
}
function successSpecificMessage(id,message)
{
    $jQu("#"+id).removeClass('alert-warning').removeClass('alert-success');
    $jQu("#"+id).addClass("alert-success").html(message);

}
$jQu(document).on('click', '.delete', function(e) {
    if(confirm('Do you want to delete this record?'))
    {
        return true;
    }
    return false;
});
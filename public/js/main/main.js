// Ajax форма
$(document).ready(function (){
    $('.ajax__form').submit(function (event) {
        var json;
        event.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) {
                json = JSON.parse(result);
                if(json.message){
                    Swal.fire({
                        html: json.message,
                        icon: 'error',
                        confirmButtonText: 'Понятно'
                    })
                }

                if(json.url){
                    document.location = json.url;
                }

                if(json.dd) {
                    console.log(json.dd);
                }
            }
        });
    });
});

tinymce.init({
    selector: '#tinymce',
    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
    toolbar_mode: 'floating',
});

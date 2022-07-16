// Ajax форма
$(document).ready(function (){
    $('.ajax__form').submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {
                console.log(result);
                if(result['message']){
                    Swal.fire({
                        html: result['message'],
                        icon: result['message_type'],
                        confirmButtonText: 'Понятно'
                    })
                }

                if(result['url']){
                    document.location = result['url'];
                }

                if(result['dd']) {
                    console.log(result['dd']);
                }
            }
        });
    });
});

// Инициализация текстового редактора
tinymce.init({
    selector: '#tinymce',
    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
    toolbar_mode: 'floating',
});

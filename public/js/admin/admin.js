// TinyMCE
tinymce.init({
    selector: '#tinymce',

    image_class_list: [
        {title: 'img-responsive', value: 'img-responsive'},
    ],
    relative_urls : false,
    height: 500,
    setup: function (editor) {
        editor.on('init change', function () {
            editor.save();
        });
    },
    plugins: [
        "advcode advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste imagetools"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
    content_css: '/css/app.css',
    image_title: true,
    automatic_uploads: true,
    images_upload_url: '/upload',
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function() {
            var file = this.files[0];

            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            };
        };
        input.click();
    }
});

function addCode() {
  let content = $('#code_content').val();
  var myContent = tinymce.activeEditor.getContent();
  let accordion = '<div class="accordion" id="accordionExample">'+
  '<div class="accordion-item">'+
  '<h2 class="accordion-header" id="headingTwo">'+
    '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">'+
      'Accordion Item #2'+
    '</button>'+
  '</h2>'+
  '<div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">'+
    '<div class="accordion-body">'+
      '<strong>This is the second items accordion body.</strong>overflow.'+
    '</div>'+
  '</div>'+
'</div>'+
  '</div>';

  let button = '<button type="button" class="btn btn-secondary">Secondary</button><br>'
  tinymce.activeEditor.setContent(myContent + content + '<br>');
}

/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

var editor =  $('#editor');

$(document).ready(function() {
    editor.summernote({
        height: 500,
        minHeight: 250,
        maxHeight: null,
        callbacks: {
            onImageUpload: function(files) {
                sendFile(files[0]);
            }
        },
         toolbar: [
            // [groupName, [list of button]]
            ['paragraph-style', ['style']],
            ['font-style', ['bold', 'italic', 'underline', 'strikethrough']],
            ['font-color', ['color']],
            ['paragraph', ['ul', 'ol', 'paragraph']],
            ['table', ['table']], 
            ['insert', ['link', 'picture', 'video']], 
            ['misc', ['codeview']]
          ],
        lang: 'ko-KR',
        focus: true
    });
});

function sendFile(file) {
    var formData = new FormData();
    formData.append("user-file", file);

    var fileData = URL.createObjectURL(file);
    $.ajax({
        url: "/yw_upload.php",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function(data) {
            if (data.charAt(0) != "/") {
                alert(data);
            } else {
                editor.summernote("insertImage", data);
            }
        }
    });
}